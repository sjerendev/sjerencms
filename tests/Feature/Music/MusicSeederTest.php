<?php

use App\Models\Release;
use App\Models\Track;
use Database\Seeders\MusicSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

it('seeds all 22 releases with clean names, covers and tracks, idempotently', function () {
    $this->seed(MusicSeeder::class);
    $this->seed(MusicSeeder::class);

    expect(Release::count())->toBe(22)->and(Track::count())->toBe(122)
        ->and(Release::published()->count())->toBe(22)
        ->and(Release::whereNotNull('id')->get()->every(fn ($r) => $r->getFirstMedia('cover') !== null))->toBeTrue()
        ->and(Release::where('artist', 'like', "%\u{200B}%")->exists())->toBeFalse();

    $voyages = Release::where('slug', 's-d-l-past-voyages')->sole();
    expect([$voyages->artist, $voyages->title, $voyages->min_price, $voyages->released_on->toDateString()])
        ->toBe(['S.D.L.', 'Past Voyages', 70, '2026-09-10'])
        ->and($voyages->tracks->pluck('title')->all())->toBe(['Big Dripper', 'Deep Down', 'Saience', 'The Worm'])
        ->and($voyages->tracks->pluck('min_price')->unique()->all())->toBe([20])
        ->and(Release::aliases()->firstWhere('name', 'S.D.L.')['count'])->toBe(6);
});
