<?php

use App\Models\Page;
use App\Models\Project;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/** The published home page for a language, holding one block. */
function blockPage(string $type, array $data, string $language = 'sv'): Page
{
    return Page::factory()->homepage()->create([
        'language' => $language,
        'content' => [['type' => $type, 'data' => $data]],
    ]);
}

/** One of every block in the builder, with the fields each needs to save. Needs two projects. */
function everyBlock(): array
{
    return [
        ['type' => 'image_text', 'data' => ['image' => 'content-images/a.jpg', 'text' => 'Image text', 'reverse_layout' => false]],
        ['type' => 'full_text', 'data' => ['text' => 'Full text']],
        ['type' => 'headline_text', 'data' => ['number' => '04', 'label' => 'Om mig', 'headline' => 'Headline', 'text' => 'Body', 'cards' => [['title' => 'Design', 'body' => '']]]],
        ['type' => 'sticky_carousel', 'data' => ['section_height' => '100vh', 'images' => [
            ['image' => 'sticky-carousel/a.jpg', 'title' => 'A', 'url' => 'https://a.se'],
            ['image' => 'sticky-carousel/b.jpg', 'title' => 'B', 'url' => 'https://b.se'],
            ['image' => 'sticky-carousel/c.jpg', 'title' => 'C', 'url' => 'https://c.se'],
        ]]],
        ['type' => 'call_to_action', 'data' => ['headline' => 'Call to action', 'text' => 'Text', 'button_text' => 'Go', 'button_url' => '/x', 'background_type' => 'color', 'background_color' => '#ffffff']],
        ['type' => 'faq_accordion', 'data' => ['number' => '04', 'label' => 'Six questions', 'items' => [['question' => 'Q', 'answer' => 'A', 'note' => 'N']]]],
        ['type' => 'service_cards', 'data' => ['number' => '03', 'label' => 'Services', 'columns' => [['title' => 'T', 'items' => "A\nB"]]]],
        ['type' => 'ticker', 'data' => ['background_color' => '#ffffff', 'text_color' => '#000000', 'angle' => 0, 'direction' => 'left-to-right', 'words' => ['a', 'b']]],
        ['type' => 'stagger_text', 'data' => ['text_row_1' => 'Stagger', 'color_row_1' => '#000000']],
        ['type' => 'colophon', 'data' => ['label' => 'Verktyg', 'tools' => 'Figma · Laravel']],
        ['type' => 'register', 'data' => ['number' => '02', 'label' => 'Register', 'aside' => '{total} totalt', 'projects' => [['project_id' => Project::orderBy('id')->first()->id], ['project_id' => Project::orderByDesc('id')->first()->id]], 'all_label' => null]],
        ['type' => 'featured_cases', 'data' => ['number' => '01', 'label' => 'Utvalda case', 'aside' => 'Tre av {total}', 'projects' => [['project_id' => Project::orderBy('id')->first()->id]]]],
        ['type' => 'hero', 'data' => ['lead' => 'Jag levererar', 'phrases' => "UI/UX-design\nFrontend", 'intro_one' => 'Ett.', 'intro_two' => 'Två.', 'work_label' => 'Se arbetet', 'cv_label' => 'Ladda ner CV', 'cv_file' => 'cv/a.pdf', 'cv_file_name' => 'Sjeren-CV.pdf']],
        ['type' => 'page_hero', 'data' => ['lines' => "Trettiosju år,\ntvå spår", 'text' => 'De {total} här.', 'size' => 'large']],
        ['type' => 'profile', 'data' => ['number' => '01', 'label' => 'Profil', 'column_one' => "Först.\n\nSedan.", 'column_two' => 'Tredje.', 'stats' => [['value' => '25', 'label' => 'Years']]]],
        ['type' => 'timeline', 'data' => ['number' => '02', 'label' => 'Experience', 'work_label' => 'Work', 'sound_label' => 'Sound', 'entries' => [['years' => '1989', 'title' => 'Ibiza', 'subtitle' => 'Trip', 'body' => 'Last.', 'tags' => ['Laravel'], 'track' => 'sound']]]],
        ['type' => 'stack', 'data' => ['number' => '03', 'label' => 'Works with', 'groups' => [['label' => 'Design', 'items' => 'Figma · Penpot']]]],
    ];
}
