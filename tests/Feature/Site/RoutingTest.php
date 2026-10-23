<?php

use App\Models\Page;

it('redirects the root to Swedish', function () {
    $this->get('/')->assertStatus(302)->assertRedirect('/se');
});

it('redirects old English slugs', function (string $from, string $to) {
    $this->get($from)->assertStatus(301)->assertRedirect($to);
})->with([
    ['/en/about-me', '/en/om-mig'],
    ['/en/about-me/', '/en/om-mig'],
    ['/en/projects', '/en/projekt'],
    ['/en/services', '/en'],
    ['/en/reputation', '/en'],
]);

it('shows the home page in each language', function () {
    Page::factory()->homepage()->create();
    Page::factory()->homepage()->english()->create();

    $this->get('/se')->assertOk()->assertSee('<html lang="sv"', false);
    $this->get('/en/')->assertOk()->assertSee('<html lang="en"', false);
});

it('serves a page with and without trailing slash', function () {
    Page::factory()->create(['slug' => 'om-mig']);

    $this->get('/se/om-mig')->assertOk();
    $this->get('/se/om-mig/')->assertOk();
});

it('answers 404 for what does not exist', function (string $url) {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->unpublished()->create(['slug' => 'utkast']);
    Page::factory()->english()->create(['slug' => 'bara-engelska']);

    $this->get($url)->assertNotFound()->assertSee('Sidan finns inte');
})->with(['/de/om-mig', '/se/finns-inte', '/se/utkast', '/se/bara-engelska', '/se/Om-Mig']);

it('keeps admin and health routes reachable', function () {
    $this->get('/admin/login')->assertOk();
    $this->get('/up')->assertOk();
});

it('falls back to the default title and description', function () {
    Page::factory()->create(['slug' => 'om-mig', 'meta_title' => null, 'meta_description' => null]);

    $this->get('/se/om-mig')
        ->assertSee('<title>Patrik Sjerén — Design och kod</title>', false)
        ->assertSee('UI/UX-designer och frontend-utvecklare i Malmö.');
});

it('links alternates only to languages where the page exists', function () {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->english()->create(['slug' => 'om-mig']);
    Page::factory()->create(['slug' => 'bara-svenska']);

    $this->get('/se/om-mig')
        ->assertSee('rel="canonical" href="'.url('/se/om-mig').'"', false)
        ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en/om-mig').'"', false);
    $this->get('/se/bara-svenska')->assertDontSee('<link rel="alternate" hreflang="en"', false);
});

it('renders a page holding a block type that has no view', function () {
    Page::factory()->create(['slug' => 'gammal', 'content' => [['type' => 'logo_showcase', 'data' => []]]]);

    $this->get('/se/gammal')->assertOk();
});

it('loads Livewire and tracks assets for wire:navigate', function () {
    Page::factory()->homepage()->create();

    expect($this->get('/se')->getContent())
        ->toMatch('#<script[^>]+livewire[^>]*\.js#')
        ->toContain('data-navigate-track');
});
