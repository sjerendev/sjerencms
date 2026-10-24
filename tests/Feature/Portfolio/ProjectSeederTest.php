<?php

use App\Models\Project;
use Database\Seeders\ProjectSeeder;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

function portfolio(): array
{
    return json_decode(file_get_contents(database_path('seeders/data/portfolio/projects.json')), true);
}

beforeEach(fn () => Storage::fake('public'));

it('seeds every project in file order with its images', function () {
    $this->seed(ProjectSeeder::class);

    $rows = portfolio()['projects'];
    expect(Project::orderBy('position')->pluck('slug')->all())->toBe(array_column($rows, 'slug'));

    $tall = array_column(array_filter($rows, fn ($r) => isset($r['tall'])), 'slug');
    Project::all()->each(function (Project $project) use ($tall) {
        expect($project->imageUrl())->not->toBeNull()
            ->and($project->previewUrl())->not->toBeNull()
            ->and($project->tall() !== null)->toBe(in_array($project->slug, $tall));
    });
});

it('maps the bilingual fields and the flags', function () {
    $this->seed(ProjectSeeder::class);

    $katarina = Project::where('slug', 'konditori-katarina')->first();
    expect($katarina->headline('sv'))->toStartWith('Konditori i Malmö.')
        ->and($katarina->role('en'))->toBe('Design & development')
        ->and($katarina->tech())->toBe('Laravel · Vue.js · Tailwind · UI/UX')
        ->and(Project::where('slug', 'champion-generators')->value('rebuilt'))->toBeTrue();
});

it('keeps edits made in the admin when it runs again', function () {
    $this->seed(ProjectSeeder::class);
    $media = Media::count();
    Project::where('slug', 'kalibr')->update(['headline_sv' => 'Ändrad i admin']);

    $this->seed(ProjectSeeder::class);

    expect(Project::where('slug', 'kalibr')->value('headline_sv'))->toBe('Ändrad i admin')
        ->and(Project::count())->toBe(count(portfolio()['projects']))
        ->and(Media::count())->toBe($media);
});
