<?php

use App\Models\Page;
use App\Settings\GeneralSettings;

beforeEach(function () {
    Page::factory()->create(['slug' => 'om-mig']);
    Page::factory()->english()->create(['slug' => 'om-mig']);
});

it('links the wordmark and nav with wire:navigate', function () {
    expect($this->get('/se/om-mig')->getContent())
        ->toContain('aria-label="Patrik Sjerén"')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se'), '#').'"[^>]*wire:navigate#')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/projekt'), '#').'"[^>]*wire:navigate[^>]*>\s*Projekt#')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/om-mig'), '#').'"[^>]*wire:navigate[^>]*>\s*Om mig#');
});

it('switches language on the same path', function () {
    expect($this->get('/se/om-mig')->getContent())
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/en/om-mig'), '#').'"[^>]*hreflang="en"#')
        ->toMatch('#aria-current="true"[^>]*>\s*SV#');
});

it('shows the bar and the call to action from settings', function () {
    $this->get('/se/om-mig')
        ->assertSee("Portfolio '26 — Malmö, Sverige")
        ->assertSee('Tillgänglig för uppdrag / anställning')
        ->assertSee('href="mailto:patrik.sjeren@gmail.com"', false)
        ->assertSee('Boka ett samtal');

    $this->get('/en/om-mig')->assertSee('Available for contract or full-time')->assertSee('Book a call');
});

it('hides the call to action when no email is set', function () {
    $settings = app(GeneralSettings::class);
    $settings->email = '';
    $settings->save();

    $this->get('/se/om-mig')->assertDontSee('mailto:', false);
});

it('points the language switch at the home page when the page has no translation', function () {
    Page::factory()->create(['slug' => 'bara-svenska']);
    Page::factory()->homepage()->english()->create();

    expect($this->get('/se/bara-svenska')->getContent())
        ->not->toContain('href="'.url('/en/bara-svenska').'"')
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/en'), '#').'"[^>]*>\\s*EN#');
});

it('links Musik and marks the current section', function () {
    \App\Models\Release::factory()->create();
    expect($this->get('/se/musik')->getContent())
        ->toMatch('#<a[^>]+href="'.preg_quote(url('/se/musik'), '#').'"[^>]*wire:navigate[^>]*aria-current="page"#');
});
