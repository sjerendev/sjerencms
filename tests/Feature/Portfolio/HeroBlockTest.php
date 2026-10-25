<?php

use App\Models\Page;

function heroData(): array
{
    return ['lead' => 'Jag levererar', 'phrases' => "UI/UX-design\nDesignsystem\nFrontend",
        'intro_one' => 'Ett.', 'intro_two' => 'Två.', 'work_label' => 'Se arbetet',
        'cv_label' => 'Ladda ner CV', 'cv_file' => 'cv/01HX.pdf', 'cv_file_name' => 'Sjeren-CV.pdf'];
}

it('renders the lead with the first phrase standing still', function () {
    Page::factory()->homepage()->create(['content' => [['type' => 'hero', 'data' => heroData()]]]);

    $html = $this->get('/se')->assertOk()->getContent();
    expect($html)->toContain('Jag levererar')
        ->toContain('<span class="sr-only">UI/UX-design</span>')
        ->toContain('data-motion="hero-rotator"')
        ->toContain('data-phrases="'.e(json_encode(['UI/UX-design', 'Designsystem', 'Frontend'])).'"')
        ->toContain('data-motion="hero-waves"')
        // The next phrase waits below the edge, so it never overlaps the first.
        ->toContain('<span data-in class="inline-block whitespace-pre" style="transform: translateY(1.35em)">D</span>');
});

it('keeps å, ä and ö whole when it splits the lines and the letters', function () {
    Page::factory()->homepage()->create(['content' => [['type' => 'hero', 'data' => ['phrases' => "Åsa\r\n\nÄng"] + heroData()]]]);

    $this->get('/se')->assertSee('<span class="sr-only">Åsa</span>', false)
        ->assertSee('<span data-out class="inline-block whitespace-pre">Å</span>', false)
        ->assertSee('data-phrases="'.e(json_encode(['Åsa', 'Äng'])).'"', false);
});

it('links to the work and the uploaded CV, under the name it was uploaded with', function () {
    // A path from before uploads is left in the data and never shown.
    $data = ['cv_url' => '/files/Sjeren-Portfolio-cv-2024.pdf'] + heroData();
    Page::factory()->homepage()->create(['content' => [['type' => 'hero', 'data' => $data]]]);

    // In order after the lead, so the header's own projekt link doesn't count.
    $this->get('/se')->assertSeeInOrder(['Jag levererar', 'href="'.url('/se/projekt').'"', 'Se arbetet'], false)
        ->assertSee('href="'.Storage::disk('public')->url('cv/01HX.pdf').'" download="Sjeren-CV.pdf"', false)
        ->assertDontSee('/files/Sjeren-Portfolio-cv-2024.pdf', false);
});

it('links to the English work on /en', function () {
    Page::factory()->homepage()->create(['language' => 'en', 'content' => [['type' => 'hero', 'data' => heroData()]]]);

    $this->get('/en')->assertSeeInOrder(['Jag levererar', 'href="'.url('/en/projekt').'"', 'Se arbetet'], false);
});

it('serves both CVs at their old addresses', function () {
    expect(file_exists(public_path('files/Sjeren-Portfolio-cv-2024.pdf')))->toBeTrue()
        ->and(file_exists(public_path('files/Sjeren-Portfolio-cv-en-2024.pdf')))->toBeTrue();
});
