<?php

use App\Models\Page;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Blade;

beforeEach(fn () => Page::factory()->homepage()->create());

it('shows the headline and every contact link from settings', function () {
    $html = $this->get('/se')->getContent();

    expect($html)->toContain('Har du något på gång?')->toContain('Kontakta mig!');
    foreach ([
        'mailto:patrik.sjeren@gmail.com' => 'patrik.sjeren@gmail.com',
        'https://www.linkedin.com/in/patrik-sjeren/' => 'LinkedIn',
        'https://www.facebook.com/sjeren' => 'Facebook',
        'https://www.youtube.com/@patriksjeren/' => 'YouTube',
        'https://patriksjeren.bandcamp.com/' => 'Bandcamp',
    ] as $href => $label) {
        expect($html)->toMatch('#<a[^>]+href="'.preg_quote($href, '#').'"[^>]*>\s*'.preg_quote($label, '#').'#');
    }
    expect($html)->toMatch('#href="https://www\.linkedin\.com[^"]*"[^>]*target="_blank"[^>]*rel="noreferrer"#');
});

it('hides a contact row whose url is empty', function () {
    $settings = app(GeneralSettings::class);
    $settings->youtube_url = null;
    $settings->save();

    $this->get('/se')->assertDontSee('YouTube');
});

it('prints the current year and place', function () {
    Page::factory()->homepage()->english()->create();
    $this->travelTo('2027-03-01');

    $this->get('/se')->assertSee('© 2027 Patrik Sjerén')->assertSee('Malmö · Sverige');
    $this->get('/en')->assertSee('Malmö · Sweden');
});

it('renders the section head with and without aside', function () {
    expect(Blade::render('<x-section-head number="01" label="Utvalda case" aside="Tre av 16" />'))
        ->toContain('>01<')->toContain('w-[26px]')->toMatch('#<h2[^>]*>\s*Utvalda case#')->toContain('Tre av 16');
    expect(Blade::render('<x-section-head number="03" label="Tjänster" />'))->not->toContain('ml-auto');
});

it('labels the email row with the address it links to', function () {
    $settings = app(GeneralSettings::class);
    $settings->email = 'ny@example.com';
    $settings->save();

    $this->get('/se')->assertSee('href="mailto:ny@example.com"', false)
        ->assertDontSee('patrik.sjeren@gmail.com');
});
