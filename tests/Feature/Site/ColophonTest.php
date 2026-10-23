<?php

use App\Models\Page;

it('renders the colophon band with the tools twice', function () {
    Page::factory()->homepage()->create(['content' => [[
        'type' => 'colophon',
        'data' => ['label' => 'Verktyg', 'tools' => 'Figma · Laravel · GSAP'],
    ]]]);

    $html = $this->get('/se')->getContent();

    expect($html)->toContain('data-motion="colophon"')->toContain('Verktyg')
        ->and(substr_count($html, '>Laravel<'))->toBe(2)
        ->and($html)->toContain('aria-hidden="true" class="flex shrink-0');
});
