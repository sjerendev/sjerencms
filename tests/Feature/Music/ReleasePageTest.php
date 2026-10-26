<?php

use App\Models\Release;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->release = Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages', 'slug' => 's-d-l-past-voyages', 'released_on' => '2026-09-10', 'min_price' => 70]);
    foreach ([['Big Dripper', 524], ['Deep Down', 621], ['Saience', 467], ['The Worm', 352]] as $i => [$title, $s]) {
        $this->release->tracks()->create(['position' => $i + 1, 'title' => $title, 'duration_seconds' => $s]);
    }
});

// Buy buttons need a WAV on every track (decision 13).
function giveWavs(Release $release): void
{
    Storage::fake('music');
    foreach ($release->tracks as $track) {
        $track->addMedia(UploadedFile::fake()->create('master.wav', 10, 'audio/wav'))->toMediaCollection('wav');
    }
}

it('shows the release with meta, tracks and the album form', function () {
    giveWavs($this->release);
    // The play control needs a track with a preview.
    Storage::fake('public');
    $this->release->tracks[0]->addMedia(UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');

    $this->get('/se/musik/s-d-l-past-voyages')->assertOk()
        ->assertSee('Past Voyages')->assertSee('10 sep 2026')->assertSee('32 min')->assertSee('WAV')
        ->assertSee('8:44')->assertSee('10:21')->assertSee('Köp · 20 kr')
        ->assertSee('Köp hela albumet')->assertSee('Du väljer, minst 70')
        ->assertSee('min="70"', false)->assertSee('data-play-release="s-d-l-past-voyages"', false);
});

it('shows more from the same alias without itself or drafts', function () {
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Vol. 3']);
    Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Dold', 'is_published' => false]);
    Release::factory()->create(['artist' => 'Monokii', 'title' => 'Annat alias']);

    $this->get('/se/musik/s-d-l-past-voyages')
        ->assertSee('Mer från S.D.L.')->assertSee('Vol. 3')->assertDontSee('Dold')->assertDontSee('Annat alias')
        ->assertSee('Alla 3 släpp →');
});

it('hides the section when the alias has nothing else and skips an empty description', function () {
    $this->get('/se/musik/s-d-l-past-voyages')->assertDontSee('Mer från')->assertDontSee('[Text om släppet');
});

it('answers 404 for a draft or unknown release', function () {
    Release::factory()->create(['slug' => 'utkast', 'is_published' => false]);
    $this->get('/se/musik/utkast')->assertNotFound();
    $this->get('/se/musik/finns-inte')->assertNotFound();
});

it('speaks English', function () {
    giveWavs($this->release);
    $this->get('/en/musik/s-d-l-past-voyages')->assertSee('10 Sep 2026')->assertSee('Buy · 20 kr');
});
