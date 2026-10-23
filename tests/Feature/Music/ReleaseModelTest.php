<?php

use App\Models\Release;
use App\Models\Track;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('sums durations and formats clocks', function () {
    $release = Release::factory()->create();
    Track::factory()->for($release)->create(['position' => 1, 'duration_seconds' => 524]);
    Track::factory()->for($release)->create(['position' => 2, 'duration_seconds' => 1440]);

    expect($release->totalSeconds())->toBe(1964)->and($release->minutes())->toBe(32)
        ->and($release->tracks[0]->clock())->toBe('8:44')
        ->and(Track::factory()->make(['duration_seconds' => 3725])->clock())->toBe('1:02:05');
});

it('defaults a track to 20 kr and orders tracks by position', function () {
    $release = Release::factory()->create();
    $release->tracks()->create(['position' => 2, 'title' => 'B', 'duration_seconds' => 60]);
    $release->tracks()->create(['position' => 1, 'title' => 'A', 'duration_seconds' => 60]);

    expect($release->tracks->pluck('title')->all())->toBe(['A', 'B'])
        ->and($release->tracks[0]->fresh()->min_price)->toBe(20);
});

it('lists aliases of published releases with counts', function () {
    Release::factory()->count(2)->create(['artist' => 'S.D.L.', 'is_published' => true]);
    Release::factory()->create(['artist' => 'Monokii', 'is_published' => true]);
    Release::factory()->create(['artist' => 'Hemlig', 'is_published' => false]);

    expect(Release::aliases()->all())->toBe([
        ['slug' => 'sdl', 'name' => 'S.D.L.', 'count' => 2],
        ['slug' => 'monokii', 'name' => 'Monokii', 'count' => 1],
    ]);
});

it('keeps the wav on the private music disk', function () {
    Storage::fake('music');
    $track = Track::factory()->create();
    $track->addMedia(UploadedFile::fake()->create('master.wav', 10, 'audio/wav'))->toMediaCollection('wav');

    expect($track->getFirstMedia('wav')->disk)->toBe('music')
        ->and(config('filesystems.disks.music.root'))->toBe(storage_path('app/music'))
        ->and(config('filesystems.disks.music'))->not->toHaveKey('url');
});

it('removes the tracks and their audio when a release is deleted', function () {
    Storage::fake('public');
    Storage::fake('music');
    $track = Track::factory()->create();
    $track->addMedia(UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');
    $track->addMedia(UploadedFile::fake()->create('m.wav', 10, 'audio/wav'))->toMediaCollection('wav');
    $preview = $track->getFirstMedia('preview')->getPathRelativeToRoot();

    $track->release->delete();

    expect(Track::count())->toBe(0)
        ->and(\Spatie\MediaLibrary\MediaCollections\Models\Media::count())->toBe(0);
    Storage::disk('public')->assertMissing($preview);
});
