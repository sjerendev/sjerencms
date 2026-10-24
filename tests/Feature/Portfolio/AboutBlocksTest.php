<?php

use App\Models\Project;

it('breaks the hero lines and fills in the total', function () {
    Project::factory()->count(2)->create();
    blockPage('page_hero', ['lines' => "Trettiosju år,\ntvå spår", 'text' => 'De {total} här.', 'size' => 'large']);

    $this->get('/se')->assertSee('Trettiosju år,<br>', false)->assertSee('De 2 här.')->assertSee('clamp(42px,8vw,96px)', false);
});

it('sizes the medium hero for the projects page', function () {
    blockPage('page_hero', ['lines' => 'Ett urval', 'text' => 'Text.', 'size' => 'medium']);

    $this->get('/se')->assertSee('clamp(38px,7vw,86px)', false)->assertDontSee('clamp(42px,8vw,96px)', false);
});

it('renders the profile columns and the margin numbers', function () {
    blockPage('profile', ['number' => '01', 'label' => 'Profil', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tredje.', 'stats' => [['value' => '25', 'label' => 'År i branschen']]]);

    $this->get('/se')->assertSeeInOrder(['Profil', 'Först.', 'Sedan.', 'Tredje.', '25', 'År i branschen'])
        ->assertSee('text-[17px] leading-[1.65]">Först.', false);
});

it('marks each timeline entry with its track and lists its tags', function () {
    blockPage('timeline', ['number' => '02', 'label' => 'Erfarenhet', 'work_label' => 'Arbete', 'sound_label' => 'Ljud', 'entries' => [
        ['years' => '1989', 'title' => 'Ibiza', 'subtitle' => 'Restresa', 'body' => 'Sista.', 'tags' => [], 'track' => 'sound'],
        ['years' => '2024 —', 'title' => 'Eget', 'subtitle' => 'Malmö', 'body' => 'Design.', 'tags' => ['Laravel'], 'track' => 'work'],
    ]]);

    $this->get('/se')->assertSee('data-track="sound"', false)->assertSee('data-track="work"', false)
        ->assertSeeInOrder(['Ibiza', 'Ljud', 'Eget', 'Laravel', 'Arbete']);
});

it('renders the stack groups', function () {
    blockPage('stack', ['number' => '03', 'label' => 'Arbetar med', 'groups' => [['label' => 'Design', 'items' => 'Figma · Penpot']]]);

    $this->get('/se')->assertSeeInOrder(['Arbetar med', 'Design', 'Figma · Penpot']);
});
