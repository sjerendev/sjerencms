<?php

use App\Models\Release;
use App\Models\Track;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('music'));

function twoTrackRelease(): Release
{
    return Release::factory()->has(Track::factory()->withWav()->count(2)->sequence(
        ['position' => 1, 'title' => 'One'],
        ['position' => 2, 'title' => 'Two/Three'],
    ))->create();
}

it('builds a zip with numbered WAVs', function () {
    $release = twoTrackRelease();

    $this->artisan('music:build-zips')->assertSuccessful();

    $media = $release->fresh()->getFirstMedia('zip');
    $zip = new ZipArchive;
    $zip->open($media->getPath());

    expect([$zip->getNameIndex(0), $zip->getNameIndex(1)])->toBe(['01 One.wav', '02 Two-Three.wav'])
        ->and($media->file_name)->toBe("{$release->slug}.zip")
        ->and($media->disk)->toBe('music')
        ->and($media->getCustomProperty('fingerprint'))->toBeString();
});

it('leaves an unchanged zip alone and rebuilds a stale one', function () {
    $release = twoTrackRelease();
    $this->artisan('music:build-zips')->assertSuccessful();
    $first = $release->fresh()->getFirstMedia('zip')->id;

    $this->artisan('music:build-zips')->assertSuccessful();
    expect($release->fresh()->getFirstMedia('zip')->id)->toBe($first);

    $release->tracks()->first()->addMedia(UploadedFile::fake()->create('new.wav', 12, 'audio/wav'))->toMediaCollection('wav');
    $this->artisan('music:build-zips')->assertSuccessful();
    expect($release->fresh()->getFirstMedia('zip')->id)->not->toBe($first);
});

it('skips a release with a track missing its WAV', function () {
    $release = twoTrackRelease();
    Track::factory()->for($release)->create(['position' => 3]);

    $this->artisan('music:build-zips')->assertSuccessful();

    expect($release->fresh()->getFirstMedia('zip'))->toBeNull();
});
