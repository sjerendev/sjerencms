<?php

use App\Models\Page;
use App\Models\Project;
use Database\Seeders\SitePagesSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

it('seeds the six site pages idempotently', function () {
    $this->seed(SitePagesSeeder::class);
    $this->seed(SitePagesSeeder::class);

    expect(Page::count())->toBe(6);
    foreach (['/se', '/en', '/se/om-mig', '/en/om-mig', '/se/projekt', '/en/projekt'] as $url) {
        $this->get($url)->assertOk();
    }
    $this->get('/en/om-mig')->assertSee('<title>About — Patrik Sjerén</title>', false);
    $this->get('/se')->assertSee('data-motion="colophon"', false);
});

it('keeps edits made in the admin when it runs again', function () {
    $this->seed(SitePagesSeeder::class);
    Page::where('slug', 'om-mig')->where('language', 'sv')->update(['meta_title' => 'Ändrad i admin']);

    $this->seed(SitePagesSeeder::class);

    expect(Page::where('slug', 'om-mig')->where('language', 'sv')->value('meta_title'))->toBe('Ändrad i admin');
});

it('leaves deleted projects and removed images alone when it runs again', function () {
    $this->seed(SitePagesSeeder::class);
    $content = Page::pluck('content', 'id')->all();
    Project::where('slug', 'auma')->first()->delete();
    Project::where('slug', 'kalibr')->first()->clearMediaCollection('tall');

    $this->seed(SitePagesSeeder::class);

    expect(Project::where('slug', 'auma')->exists())->toBeFalse()
        ->and(Project::where('slug', 'kalibr')->first()->getFirstMedia('tall'))->toBeNull()
        ->and(Page::pluck('content', 'id')->all())->toEqual($content);
});

it('runs again after a featured project is deleted', function () {
    $this->seed(SitePagesSeeder::class);
    Project::where('slug', 'cado-aqua')->first()->delete();

    $this->seed(SitePagesSeeder::class);

    expect(Project::where('slug', 'cado-aqua')->exists())->toBeFalse();
});

it('still refuses to fill a page with a project that is gone', function () {
    $this->seed(SitePagesSeeder::class);
    Project::where('slug', 'cado-aqua')->first()->delete();
    Page::where('is_homepage', true)->where('language', 'sv')->update(['content' => '[]']);

    $this->seed(SitePagesSeeder::class);
})->throws(RuntimeException::class, 'Missing project cado-aqua');
