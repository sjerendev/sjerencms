<?php

use App\Models\Page;
use App\Models\Release;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('persists one player bar in the layout', function () {
    Page::factory()->homepage()->create();
    expect($this->get('/se')->getContent())->toMatch('#x-persist="player"#')->toContain('<audio');
});

it('has a close button in the bar and the mini player, in each language', function () {
    Page::factory()->homepage()->create();
    Page::factory()->homepage()->create(['language' => 'en']);

    expect(substr_count($this->get('/se')->getContent(), 'data-player-close aria-label="Stäng spelaren"'))->toBe(2)
        ->and(substr_count($this->get('/en')->getContent(), 'data-player-close aria-label="Close the player"'))->toBe(2);
});

it('only offers play for tracks that have a preview', function () {
    Storage::fake('public');
    $release = Release::factory()->create(['slug' => 'med-ljud']);
    $withPreview = $release->tracks()->create(['position' => 1, 'title' => 'Hörs', 'duration_seconds' => 60]);
    $withPreview->addMedia(UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');
    $release->tracks()->create(['position' => 2, 'title' => 'Tyst', 'duration_seconds' => 60]);
    Release::factory()->create(['slug' => 'utan-ljud'])->tracks()->create(['position' => 1, 'title' => 'Inget', 'duration_seconds' => 60]);

    $show = $this->get('/se/musik/med-ljud')->getContent();
    expect($show)->toContain('data-play-track="'.$withPreview->id.'"')
        ->and(substr_count($show, 'data-play-track='))->toBe(1);

    $index = $this->get('/se/musik')->getContent();
    $data = json_decode(Str::between($index, '<script type="application/json" data-music>', '</script>'), true);
    expect($data)->toHaveKey('med-ljud')->not->toHaveKey('utan-ljud')
        ->and($data['med-ljud']['tracks'])->toHaveCount(1)
        ->and($index)->toContain('data-play-release="med-ljud"')->not->toContain('data-play-release="utan-ljud"');
});

it('offers no play control on a release without previews', function () {
    Release::factory()->create(['slug' => 'tyst'])->tracks()->create(['position' => 1, 'title' => 'Inget', 'duration_seconds' => 60]);

    expect($this->get('/se/musik/tyst')->getContent())
        ->not->toContain('data-play-release=')->not->toContain('data-play-track=')->not->toContain('data-scrub-track=');
});
