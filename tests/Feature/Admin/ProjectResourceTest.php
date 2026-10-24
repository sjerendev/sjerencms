<?php

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

it('lists the projects for an admin', function () {
    $this->actingAs(User::factory()->admin()->create());
    $project = Project::factory()->create();

    $this->get('/admin/projects')->assertOk()->assertSee($project->client);
});

it('creates a project', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateProject::class)
        ->fillForm([
            'client' => 'Cado Aqua', 'slug' => 'cado-aqua',
            'headline_sv' => 'En rubrik', 'headline_en' => 'A headline',
            'services' => ['Wordpress', 'UI-design'], 'url' => 'https://cadoaqua.se', 'position' => 1,
        ])
        ->call('create')->assertHasNoFormErrors();

    expect(Project::sole()->services)->toBe(['Wordpress', 'UI-design']);
});

it('requires the English headline', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateProject::class)
        ->fillForm(['client' => 'X', 'slug' => 'x', 'headline_sv' => 'Y', 'services' => ['A'], 'url' => 'https://x.se', 'position' => 1])
        ->call('create')->assertHasFormErrors(['headline_en' => 'required']);
});
