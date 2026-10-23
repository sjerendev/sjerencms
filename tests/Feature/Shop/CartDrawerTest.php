<?php

use App\Livewire\CartDrawer;
use App\Models\Release;
use App\Models\Track;
use App\Shop\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('music');
    // Livewire::test skips the HTTP middleware that sets these.
    app()->setLocale('sv');
    URL::defaults(['locale' => 'se']);
    $this->release = Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Past Voyages', 'slug' => 's-d-l-past-voyages', 'min_price' => 70]);
    $this->track = Track::factory()->for($this->release)->withWav()->create(['title' => 'Deep Down', 'position' => 1, 'min_price' => 20]);
});

it('adds an item from the cart-add event and opens', function () {
    Livewire::test(CartDrawer::class)
        ->assertSee('[Korgen är tom]')
        ->dispatch('cart-add', type: 'track', id: $this->track->id, amount: 25)
        ->assertSee('Deep Down')->assertSee('S.D.L. · Past Voyages')->assertSee('25 kr')
        ->assertSee('Spår · WAV · minst 20 kr')->assertSee('Korg · 1 spår')
        ->assertDontSee('[Korgen är tom]')
        ->assertDispatched('cart-open')
        ->assertDispatched('cart-updated', count: 1);
});

it('does not open for something that cannot be bought', function () {
    Livewire::test(CartDrawer::class)
        ->dispatch('cart-add', type: 'track', id: 999, amount: 25)
        ->assertNotDispatched('cart-open')
        ->assertDispatched('cart-updated', count: 0);
});

it('shows an album with its track count and a checkout link', function () {
    $this->release->update(['min_price' => 80]);

    Livewire::test(CartDrawer::class)
        ->dispatch('cart-add', type: 'release', id: $this->release->id, amount: 80)
        ->assertSee('Past Voyages')->assertSee('Album · 1 spår · WAV · minst 80 kr')
        ->assertSee('Korg · 1 album')->assertDontSee('Korg · 1 spår')
        ->assertSee('Till kassan')->assertSee(route('shop.checkout'), false);
});

it('raises an amount below the minimum to the minimum', function () {
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CartDrawer::class)
        ->call('updateAmount', "track:{$this->track->id}", '5')
        ->assertSee('value="20"', false)->assertDontSee('value="5"', false)
        ->assertDispatched('cart-updated', count: 1);

    expect(app(Cart::class)->total())->toBe(20);
});

it('removes a line and shows the empty cart', function () {
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CartDrawer::class)
        ->assertSee('Deep Down')->assertSee('Till kassan')
        ->call('remove', "track:{$this->track->id}")
        ->assertSee('[Korgen är tom]')->assertDontSee('Deep Down')->assertDontSee('Till kassan')
        ->assertDispatched('cart-updated', count: 0);

    expect(app(Cart::class)->count())->toBe(0);
});

it('speaks English', function () {
    app()->setLocale('en');
    URL::defaults(['locale' => 'en']);
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CartDrawer::class)
        ->assertSee('Cart · 1 track')->assertSee('Track · WAV · at least 20 kr')
        ->assertSee('To checkout')->assertSee('Keep listening')
        ->call('remove', "track:{$this->track->id}")
        ->assertSee('[EN] [Korgen är tom]');
});

it('offers the buy buttons only for what has a WAV', function () {
    Track::factory()->for($this->release)->create(['title' => 'Tyst', 'position' => 2]);
    $without = Release::factory()->create(['slug' => 'utan-wav']);
    Track::factory()->for($without)->create();

    // The player bar (always in the layout, filled by player.js) comes after the page.
    $page = fn (string $url) => Str::before($this->get($url)->assertOk()->getContent(), 'x-persist="player"');

    // Not every track has a WAV, so the album cannot be bought, only the first track.
    expect($page('/se/musik/s-d-l-past-voyages'))
        ->toContain('data-cart-add="track"')->toContain('data-id="'.$this->track->id.'"')
        ->not->toContain('data-cart-add="release"')->not->toContain('data-amount-input')
        ->and(substr_count($page('/se/musik/s-d-l-past-voyages'), 'data-cart-add='))->toBe(1)
        ->and($page('/se/musik/utan-wav'))->not->toContain('data-cart-add');
});

it('offers the album when every track has a WAV', function () {
    $html = $this->get('/se/musik/s-d-l-past-voyages')->assertOk()->getContent();

    expect($html)->toContain('data-cart-add="release"')->toContain('data-amount-input')
        ->and($this->get('/se/musik')->getContent())->toContain('data-cart-add="release"');
});

it('puts the cart button with its count in the header', function () {
    $cart = ["track:{$this->track->id}" => ['type' => 'track', 'id' => $this->track->id, 'amount' => 35]];

    expect($this->withSession(['cart' => $cart])->get('/se/musik')->getContent())
        ->toMatch('#data-cart-open[^>]*>\s*Korg · <span data-cart-count>1</span>#')
        ->toContain('data-cart-dialog')->toContain('wire:ignore.self');
});
