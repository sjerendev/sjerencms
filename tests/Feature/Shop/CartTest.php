<?php

use App\Models\Release;
use App\Models\Track;
use App\Shop\Cart;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('music'));

it('adds a track at the amount the buyer chose', function () {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);

    expect(app(Cart::class)->add('track', $track->id, 35))->toBeTrue()
        ->and(app(Cart::class)->total())->toBe(35);
});

it('raises amounts below the minimum and caps huge ones', function ($amount, $expected) {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);

    app(Cart::class)->add('track', $track->id, $amount);

    expect(app(Cart::class)->total())->toBe($expected);
})->with([[5, 20], [-50, 20], ['abc', 20], [20.9, 20], [999999999, 100000], ['1e30', 100000], [[], 20]]);

it('ignores what cannot be bought', function ($type, $id) {
    expect(app(Cart::class)->add($type, $id, 100))->toBeFalse()
        ->and(app(Cart::class)->count())->toBe(0);
})->with([
    'unknown type' => fn () => ['user', 1],
    'missing id' => fn () => ['track', 999],
    'id that is not a whole number' => fn () => ['track', '1abc'],
    'no WAV' => fn () => ['track', Track::factory()->create()->id],
    'unpublished' => fn () => ['release', Release::factory()->has(Track::factory()->withWav())->create(['is_published' => false])->id],
    'album with a track missing its WAV' => fn () => ['release', Release::factory()
        ->has(Track::factory()->withWav()->state(['position' => 1]))
        ->has(Track::factory()->state(['position' => 2]))
        ->create()->id],
]);

it('replaces the amount when the same item is added again', function () {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);

    app(Cart::class)->add('track', $track->id, 30);
    app(Cart::class)->add('track', $track->id, 50);

    expect(app(Cart::class)->total())->toBe(50)->and(app(Cart::class)->count())->toBe(1);
});

it('sells an album and a track side by side', function () {
    $release = Release::factory()->has(Track::factory()->withWav())->create(['min_price' => 80]);
    $track = Track::factory()->withWav()->create(['min_price' => 20]);

    app(Cart::class)->add('release', $release->id, 80);
    app(Cart::class)->add('track', $track->id, 25);

    $lines = app(Cart::class)->lines();

    expect($lines->map->type()->all())->toBe(['release', 'track'])
        ->and($lines->map->minimum()->all())->toBe([80, 20])
        ->and(app(Cart::class)->total())->toBe(105);
});

it('keeps the minimum when an amount is changed, and removes and clears', function () {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);
    $other = Track::factory()->withWav()->create(['min_price' => 30]);
    $cart = app(Cart::class);
    $cart->add('track', $track->id, 40);
    $cart->add('track', $other->id, 40);

    $cart->setAmount("track:{$track->id}", 3);
    $cart->setAmount('track:12345', 90);

    expect($cart->total())->toBe(20 + 40);

    $cart->remove("track:{$track->id}");
    expect($cart->count())->toBe(1);

    $cart->clear();
    expect($cart->count())->toBe(0)->and(session('cart'))->toBeEmpty();
});

it('drops a line whose release was unpublished after it was added', function () {
    $release = Release::factory()->has(Track::factory()->withWav())->create();
    app(Cart::class)->add('release', $release->id, 100);

    $release->update(['is_published' => false]);

    expect(app(Cart::class)->lines())->toBeEmpty()
        ->and(session('cart'))->toBeEmpty()
        ->and(app(Cart::class)->total())->toBe(0);
});

it('drops a track whose WAV was removed', function () {
    $track = Track::factory()->withWav()->create();
    app(Cart::class)->add('track', $track->id, 100);

    $track->clearMediaCollection('wav');

    expect(app(Cart::class)->count())->toBe(0);
});

it('drops a line whose item was deleted', function () {
    $track = Track::factory()->withWav()->create();
    app(Cart::class)->add('track', $track->id, 100);

    $track->delete();

    expect(app(Cart::class)->count())->toBe(0);
});

it('raises a stored amount when the minimum was raised after the add', function () {
    $track = Track::factory()->withWav()->create(['min_price' => 20]);
    app(Cart::class)->add('track', $track->id, 20);

    $track->update(['min_price' => 50]);

    expect(app(Cart::class)->total())->toBe(50)
        ->and(app(Cart::class)->lines()->first()->amount)->toBe(50);
});

it('drops malformed entries from the session', function ($entry) {
    $track = Track::factory()->withWav()->create();
    app(Cart::class)->add('track', $track->id, 40);
    session()->put('cart.broken', $entry);

    expect(app(Cart::class)->lines())->toHaveCount(1)
        ->and(array_keys(session('cart')))->toBe(["track:{$track->id}"]);
})->with([
    'missing id' => [['type' => 'track', 'amount' => 20]],
    'missing amount' => [['type' => 'track', 'id' => 1]],
    'missing type' => [['id' => 1, 'amount' => 20]],
    'unknown type' => [['type' => 'user', 'id' => 1, 'amount' => 20]],
    'id of the wrong type' => [['type' => 'track', 'id' => ['x'], 'amount' => 20]],
    'amount of the wrong type' => [['type' => 'track', 'id' => 1, 'amount' => 'abc']],
    'not an array' => ['track'],
]);
