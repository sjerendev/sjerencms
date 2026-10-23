<?php

use App\Models\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

beforeEach(fn () => Page::factory()->homepage()->create());

it('sets the theme in a blocking head script before any stylesheet', function () {
    $html = $this->get('/se')->getContent();
    $head = Str::between($html, '<head>', '</head>');

    expect($head)->toContain('<script>(function(){try{var t=localStorage.getItem("theme");')
        ->and(strpos($head, 'localStorage.getItem("theme")'))->toBeLessThan(strpos($head, 'rel="stylesheet"'));
});

it('renders the theme toggle with translated labels', function () {
    app()->setLocale('sv');
    expect(Blade::render('<x-theme-toggle />'))
        ->toContain('aria-label="Byt läge"')->toContain('data-theme-pick="light"')
        ->toContain('aria-label="Ljust läge"')->toContain('aria-label="Mörkt läge"');

    app()->setLocale('en');
    expect(Blade::render('<x-theme-toggle />'))->toContain('aria-label="Light mode"');
});
