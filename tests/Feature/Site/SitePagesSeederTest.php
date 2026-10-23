<?php

use App\Models\Page;
use Database\Seeders\SitePagesSeeder;

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
