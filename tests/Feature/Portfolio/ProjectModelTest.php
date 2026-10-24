<?php

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('derives the role from the services in both languages', function () {
    $design = Project::factory()->create(['services' => ['Wordpress', 'UI-design']]);
    $dev = Project::factory()->create(['services' => ['Wordpress', 'Live Canvas']]);

    expect($design->role('sv'))->toBe('Design & utveckling')
        ->and($design->role('en'))->toBe('Design & development')
        ->and($dev->role('sv'))->toBe('Utveckling')
        ->and($dev->role('en'))->toBe('Development');
});

it('prefers a role that is set', function () {
    $project = Project::factory()->create(['role_sv' => 'Design & frontend', 'role_en' => 'Design & frontend', 'services' => ['Wordpress']]);

    expect($project->role('sv'))->toBe('Design & frontend');
});

it('joins the services as tech when none is set', function () {
    expect(Project::factory()->create(['services' => ['Laravel', 'Vue.js'], 'tech' => null])->tech())->toBe('Laravel · Vue.js');
});

it('strips scheme, www and the trailing slash from the host', function () {
    expect(Project::factory()->create(['url' => 'https://www.cadoaqua.se/'])->host())->toBe('cadoaqua.se');
});

it('returns the headline in the given language', function () {
    $project = Project::factory()->create(['headline_sv' => 'Svenska', 'headline_en' => 'English']);

    expect($project->headline('sv'))->toBe('Svenska')->and($project->headline('en'))->toBe('English');
});

it('returns chosen projects in the given order and skips missing ones', function () {
    [$a, $b, $c] = Project::factory()->count(3)->sequence(fn ($s) => ['position' => $s->index + 1])->create();

    expect(Project::chosen([$c->id, $a->id, 999])->pluck('id')->all())->toBe([$c->id, $a->id])
        ->and(Project::chosen([])->pluck('id')->all())->toBe([$a->id, $b->id, $c->id]);
});

it('replaces {total} with the number of projects', function () {
    Project::factory()->count(2)->create();

    expect(Project::withTotal('Tre av {total}'))->toBe('Tre av 2');
});

it('reads the size of the tall image from the file', function () {
    Storage::fake('public');
    $project = Project::factory()->create();
    $project->addMedia(UploadedFile::fake()->image('tall.png', 40, 120))->toMediaCollection('tall');

    expect($project->fresh()->tall())->toMatchArray(['width' => 40, 'height' => 120])
        ->and(Project::factory()->create()->tall())->toBeNull()
        ->and(Project::factory()->create()->previewUrl())->toBeNull()
        ->and(Project::factory()->create()->imageUrl())->toBeNull();
});

it('has factory states for rebuilt and a set role', function () {
    expect(Project::factory()->rebuilt()->make()->rebuilt)->toBeTrue()
        ->and(Project::factory()->withRole()->make()->role_sv)->not->toBeNull();
});
