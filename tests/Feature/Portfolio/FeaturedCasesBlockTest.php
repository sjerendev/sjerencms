<?php

use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/** @param list<int> $ids */
function featuredPage(array $ids, string $language = 'sv'): Page
{
    return blockPage('featured_cases', [
        'number' => '01',
        'label' => 'Utvalda case',
        'aside' => 'Tre av {total}',
        'projects' => array_map(fn (int $id) => ['project_id' => $id], $ids),
    ], $language);
}

it('pins a case that has a tall image and crops one that has not', function () {
    Storage::fake('public');
    [$tall, $flat] = Project::factory()->count(2)->create();
    $tall->addMedia(UploadedFile::fake()->image('t.png', 40, 120))->toMediaCollection('tall');
    $flat->addMedia(UploadedFile::fake()->image('f.png', 40, 30))->toMediaCollection('image');
    featuredPage([$tall->id, $flat->id]);

    $html = $this->get('/se')->assertOk()->getContent();
    expect(substr_count($html, 'data-motion="case-pin"'))->toBe(1)
        ->and($html)->toContain('width="40" height="120"')
        ->toContain('01 / 02')->toContain('02 / 02');
});

it('counts only the cases it renders', function () {
    [$a, $b, $c] = Project::factory()->count(3)->create();
    featuredPage([$a->id, $b->id, $c->id]);
    $c->delete();

    $this->get('/se')->assertOk()->assertSee('02 / 02')->assertDontSee('/ 03');
});

it('shows the meta row with the host link, or no link for a rebuilt site', function () {
    $live = Project::factory()->create(['url' => 'https://www.cadoaqua.se/', 'services' => ['UI-design']]);
    $old = Project::factory()->rebuilt()->create();
    featuredPage([$live->id, $old->id]);

    $this->get('/se')->assertSee(['Roll', 'Design & utveckling', 'cadoaqua.se', 'Ombyggd sedan dess'])
        ->assertSee('href="https://www.cadoaqua.se/"', false);
});

it('uses the English labels and role on /en', function () {
    $project = Project::factory()->create(['services' => ['Laravel']]);
    featuredPage([$project->id], 'en');

    $this->get('/en')->assertSee(['Role', 'Development', $project->headline_en]);
});
