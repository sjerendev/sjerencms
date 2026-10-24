<?php

use App\Models\Page;

$hero = fn (array $motion = []) => ['lead' => 'Jag levererar', 'phrases' => "A\nB", 'intro_one' => 'Ett <b>fett</b>.',
    'intro_two' => 'Två.', 'work_label' => 'W', 'cv_label' => 'C', 'cv_url' => '/x.pdf', 'motion' => $motion];

it('prints no motion attributes when nothing is turned on', function () use ($hero) {
    blockPage('hero', $hero());
    $this->get('/se')->assertDontSee('data-text-motion', false)->assertDontSee('data-motion="text"', false);
});

it('puts the motion on the text and keeps it escaped', function () use ($hero) {
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]));
    $this->get('/se')->assertSee('data-motion="text"', false)->assertSee('Ett &lt;b&gt;fett&lt;/b&gt;.', false);
});

it('gives the English page the Swedish motion, block by block', function () use ($hero) {
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]));
    blockPage('hero', $hero(['intro_two' => ['enabled' => true]]), 'en');
    // Only intro_one moves on /en: the English page's own motion is ignored.
    expect(substr_count($this->get('/en')->getContent(), 'data-motion="text"'))->toBe(1);
});

it('leaves English text still when the Swedish page differs or is missing', function (callable $swedish) use ($hero) {
    $swedish();
    blockPage('hero', $hero(['intro_one' => ['enabled' => true]]), 'en');
    $this->get('/en')->assertOk()->assertDontSee('data-motion="text"', false);
})->with([
    'no Swedish page' => [fn () => null],
    'unpublished' => [fn () => Page::factory()->homepage()->unpublished()->create(['content' => [['type' => 'hero', 'data' => ['motion' => ['intro_one' => ['enabled' => true]]]]]])],
    'other block type' => [fn () => blockPage('stack', ['number' => '1', 'label' => 'L', 'groups' => [], 'motion' => ['intro_one' => ['enabled' => true]]])],
]);

it('starts every repeater row on its own', function () {
    blockPage('faq_accordion', ['number' => '04', 'label' => 'Q', 'items' => [
        ['question' => 'A?', 'answer' => 'A.', 'note' => ''], ['question' => 'B?', 'answer' => 'B.', 'note' => ''],
    ], 'motion' => ['items_answer' => ['enabled' => true], 'items_note' => ['enabled' => true]]]);
    // Two answers and two empty notes, each its own motion; the empty note renders without error.
    expect(substr_count($this->get('/se')->getContent(), 'data-motion="text"'))->toBe(4);
});

it('marks each paragraph of a multi-element field as a piece', function () {
    blockPage('profile', ['number' => '01', 'label' => 'P', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tre.',
        'stats' => [], 'motion' => ['column_one' => ['enabled' => true]]]);
    $html = $this->get('/se')->getContent();
    expect(substr_count($html, 'data-motion="text"'))->toBe(1)->and(substr_count($html, 'data-motion-piece'))->toBe(2);
});

it('marks no pieces when motion is off, so the HTML stays as before', function () {
    blockPage('profile', ['number' => '01', 'label' => 'P', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tre.', 'stats' => []]);
    $this->get('/se')->assertDontSee('data-motion-piece', false);
});

it('moves the section head as one row', function () {
    blockPage('stack', ['number' => '03', 'label' => 'Arbetar med', 'groups' => [], 'motion' => ['head' => ['enabled' => true]]]);
    expect(substr_count($this->get('/se')->getContent(), 'data-motion-piece'))->toBe(3); // number, line, label; no aside
});
