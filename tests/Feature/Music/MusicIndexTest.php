<?php

use App\Models\Release;

beforeEach(function () {
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Vol. 1', 'released_on' => '2019-09-18', 'min_price' => 50]);
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages', 'released_on' => '2026-09-10', 'min_price' => 70]);
    Release::factory()->create(['artist' => 'Monokii', 'title' => 'Framtida Skuggor', 'released_on' => '2016-08-11']);
    Release::factory()->create(['artist' => 'Hemlig', 'title' => 'Utkastet', 'is_published' => false]);
});

it('lists published releases newest first with price and year', function () {
    $html = $this->get('/se/musik')->assertOk()->assertDontSee('Utkastet')->getContent();

    expect(strpos($html, 'Past Voyages'))->toBeLessThan(strpos($html, 'Vol. 1'))
        ->and(strpos($html, 'Vol. 1'))->toBeLessThan(strpos($html, 'Framtida Skuggor'))
        ->and($html)->toContain('Köp · 70 kr')->toContain('2026 ·')
        ->toContain('Sju alias<br>och mitt<br>eget namn');
});

it('filters on alias and counts only published releases', function () {
    $this->get('/se/musik?alias=sdl')->assertSee('Past Voyages')->assertDontSee('Framtida Skuggor');
    expect($this->get('/se/musik')->getContent())
        ->toMatch('#Alla\s*</span>\s*<span[^>]*>\s*3#')
        ->not->toContain('Hemlig');
});

it('shows everything for an unknown alias', function () {
    $this->get('/se/musik?alias=finns-inte')->assertOk()->assertSee('Framtida Skuggor')->assertSee('Past Voyages');
});

it('speaks English on the English list', function () {
    $this->get('/en/musik')->assertOk()->assertSee('All releases')->assertSee('Buy · 70 kr')
        ->assertSee('<html lang="en"', false);
});
