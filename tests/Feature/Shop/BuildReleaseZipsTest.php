<?php

use App\Models\Release;
use App\Models\Track;
use Illuminate\Console\Scheduling\Schedule;
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

it('keeps building the other releases when one WAV file is gone from the disk', function () {
    $broken = twoTrackRelease();
    $healthy = Release::factory()->has(Track::factory()->withWav())->create();
    $wav = $broken->tracks->first()->getFirstMedia('wav');
    Storage::disk('music')->delete($wav->getPathRelativeToRoot());

    $this->artisan('music:build-zips')->assertFailed();

    expect($broken->fresh()->getFirstMedia('zip'))->toBeNull()
        ->and($healthy->fresh()->getFirstMedia('zip'))->not->toBeNull()
        ->and(glob(storage_path('app/tmp/release-*.zip')))->toBe([]);
});

it('lets the scheduler lock expire after 30 minutes', function () {
    $event = collect(app(Schedule::class)->events())->first(fn ($e) => str_contains($e->command, 'music:build-zips'));

    expect($event->expiresAt)->toBe(30);
});
