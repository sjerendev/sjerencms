<?php

use App\Models\Project;

it('lists the chosen projects in order with zero-padded numbers', function () {
    [$a, $b] = Project::factory()->count(2)->sequence(['client' => 'Alfa', 'headline_sv' => 'Rubrik A'], ['client' => 'Beta'])->create();
    blockPage('register', ['number' => '02', 'label' => 'Fler utvalda uppdrag', 'aside' => '{total} totalt', 'projects' => [['project_id' => $b->id], ['project_id' => $a->id]]]);

    $this->get('/se')->assertOk()->assertSeeInOrder(['/ 001', 'Beta', '/ 002', 'Alfa', 'Rubrik A'])->assertSee('2 totalt');
});

it('lists every project in position order when none are chosen', function () {
    Project::factory()->count(3)->sequence(fn ($s) => ['client' => "Kund {$s->index}", 'position' => 3 - $s->index])->create();
    blockPage('register', ['number' => '01', 'label' => 'Uppdrag', 'aside' => '{total}', 'projects' => []]);

    $this->get('/se')->assertSeeInOrder(['Kund 2', 'Kund 1', 'Kund 0']);
});

it('never links a rebuilt site', function () {
    $rebuilt = Project::factory()->rebuilt()->create(['url' => 'https://gammal.se/']);
    blockPage('register', ['number' => '02', 'label' => 'L', 'aside' => '', 'projects' => [['project_id' => $rebuilt->id]]]);

    $this->get('/se')->assertDontSee('href="https://gammal.se/"', false)->assertSee('<div data-register-row', false);
});

it('skips a deleted project and leaves no empty image', function () {
    [$a, $b] = Project::factory()->count(2)->create();
    blockPage('register', ['number' => '02', 'label' => 'L', 'aside' => '{total} totalt', 'projects' => [['project_id' => $a->id], ['project_id' => $b->id]]]);
    $b->delete();

    $this->get('/se')->assertOk()->assertSee('1 totalt')->assertDontSee('src=""', false);
});

it('links to all projects and uses the English headline on /en', function () {
    $project = Project::factory()->create(['headline_en' => 'English line']);
    blockPage('register', ['number' => '02', 'label' => 'More', 'aside' => '', 'all_label' => 'See all {total}', 'projects' => [['project_id' => $project->id]]], 'en');

    $this->get('/en')->assertSee('English line')->assertSee('See all 1')->assertSee('href="'.url('/en/projekt').'"', false);
});
