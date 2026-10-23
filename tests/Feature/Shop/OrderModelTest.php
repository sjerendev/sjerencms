<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(fn () => Storage::fake('music'));

it('sells a release only when it is published and every track has a WAV', function () {
    $release = Release::factory()->has(Track::factory()->withWav()->count(2)->sequence(['position' => 1], ['position' => 2]))->create();
    expect($release->isPurchasable())->toBeTrue();

    Track::factory()->for($release)->create(['position' => 3]); // no WAV
    expect($release->fresh()->isPurchasable())->toBeFalse();
});

it('does not sell an unpublished release, an empty release, or a track without a WAV', function () {
    $draft = Release::factory()->has(Track::factory()->withWav())->create(['is_published' => false]);
    $empty = Release::factory()->create();
    $silent = Track::factory()->create();

    expect($draft->isPurchasable())->toBeFalse()
        ->and($draft->tracks[0]->isPurchasable())->toBeFalse()
        ->and($empty->isPurchasable())->toBeFalse()
        ->and($silent->isPurchasable())->toBeFalse()
        ->and(Track::factory()->withWav()->create()->isPurchasable())->toBeTrue();
});

it('pads the order number', fn () => expect(Order::factory()->create(['id' => 42])->number())->toBe('00042'));

it('knows which orders are paid', function () {
    Order::factory()->create();
    $paid = Order::factory()->paid()->create();

    expect(Order::paid()->pluck('id')->all())->toBe([$paid->id])
        ->and($paid->status)->toBe(\App\Enums\OrderStatus::Paid);
});

it('reaches the order and the bought thing from an item', function () {
    $track = Track::factory()->withWav()->create();
    $item = OrderItem::factory()->for(Order::factory())->create(['purchasable_type' => Track::class, 'purchasable_id' => $track->id]);

    expect($item->purchasable->is($track))->toBeTrue()
        ->and($item->order->items->pluck('id')->all())->toBe([$item->id])
        ->and($track->sales->pluck('id')->all())->toBe([$item->id])
        ->and($track->isSold())->toBeTrue();
});

it('counts a release as sold when it or any of its tracks was bought', function () {
    $release = Release::factory()->has(Track::factory()->withWav())->create();
    expect($release->isSold())->toBeFalse();

    OrderItem::factory()->for(Order::factory())->create(['purchasable_type' => Track::class, 'purchasable_id' => $release->tracks[0]->id]);
    expect($release->fresh()->isSold())->toBeTrue();

    $album = Release::factory()->create();
    OrderItem::factory()->for(Order::factory())->create(['purchasable_type' => Release::class, 'purchasable_id' => $album->id]);
    expect($album->isSold())->toBeTrue()->and(Release::factory()->create()->isSold())->toBeFalse();
});

it('refuses to delete a sold release or track', function () {
    $track = Track::factory()->withWav()->create();
    OrderItem::factory()->for(Order::factory())->create(['purchasable_type' => Track::class, 'purchasable_id' => $track->id]);

    $track->release->delete();

    expect(Release::find($track->release_id))->not->toBeNull()
        ->and(Track::find($track->id))->not->toBeNull()
        ->and($track->delete())->toBeFalse()
        ->and(Track::find($track->id))->not->toBeNull();
});

it('still deletes a release nobody bought', function () {
    $track = Track::factory()->withWav()->create();

    expect($track->release->delete())->toBeTrue()
        ->and(Release::count())->toBe(0)->and(Track::count())->toBe(0);
});

it('marks tracks without a WAV as not buyable in the playlist', function () {
    URL::defaults(['locale' => 'se']); // playlist() builds the release URL
    $release = Release::factory()->create();
    $track = Track::factory()->for($release)->create();
    $track->addMedia(\Illuminate\Http\UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');
    $buyable = Track::factory()->for($release)->withWav()->create(['position' => 2]);
    $buyable->addMedia(\Illuminate\Http\UploadedFile::fake()->create('p.mp3', 10, 'audio/mpeg'))->toMediaCollection('preview');

    $tracks = $release->fresh()->playlist()['tracks'];

    expect($tracks[0]['buyable'])->toBeFalse()->and($tracks[1]['buyable'])->toBeTrue();
});
