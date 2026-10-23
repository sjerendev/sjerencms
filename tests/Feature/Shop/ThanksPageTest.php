<?php

use App\Enums\OrderStatus;
use App\Livewire\OrderStatusPanel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use App\Shop\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('music');
    // Livewire::test skips the HTTP middleware that sets these.
    app()->setLocale('sv');
    URL::defaults(['locale' => 'se']);

    $this->track = Track::factory()->withWav()->create(['title' => 'Deep Down']);
    $this->order = Order::factory()->create(['email' => 'name@example.se', 'total' => 100]);
    $this->item = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Track::class,
        'purchasable_id' => $this->track->id,
        'title' => 'Deep Down',
    ]);
    $this->url = "/se/kassa/tack/{$this->order->id}";
});

it('hides an order from browsers that did not start it', function () {
    $this->get($this->url)->assertNotFound();
    $this->withSession(['orders' => [$this->order->id + 1]])->get($this->url)->assertNotFound();
});

it('compares the session ids strictly', function () {
    $this->withSession(['orders' => [(string) $this->order->id]])->get($this->url)->assertNotFound();
});

it('lets the owner in and nobody else who is logged in', function () {
    $owner = User::factory()->create();
    $this->order->update(['user_id' => $owner->id]);

    $this->actingAs($owner)->get($this->url)->assertOk();
    $this->actingAs(User::factory()->create())->get($this->url)->assertNotFound();
});

it('does not open an order without an owner to a logged-in stranger', function () {
    $this->actingAs(User::factory()->create())->get($this->url)->assertNotFound();
});

it('has no page for a refunded order', function () {
    $this->order->update(['status' => OrderStatus::Refunded]);

    $this->withSession(['orders' => [$this->order->id]])->get($this->url)->assertNotFound();
});

it('shows a processing state and polls until the webhook comes', function () {
    $this->withSession(['orders' => [$this->order->id]])->get($this->url)
        ->assertSee(__('shop.processing'))->assertSee('wire:poll', false)
        ->assertDontSee(route('download', $this->item));
});

it('stops polling and shows the downloads when the order turns paid', function () {
    $panel = Livewire::test(OrderStatusPanel::class, ['order' => $this->order])
        ->assertSee(__('shop.processing'))->assertSeeHtml('wire:poll');

    $this->order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

    $panel->call('$refresh')
        ->assertDontSee(__('shop.processing'))->assertDontSeeHtml('wire:poll')
        ->assertSee('5 av 5 kvar')->assertSee(route('download', $this->item));
});

it('does not take the order from the browser', function () {
    expect(fn () => Livewire::test(OrderStatusPanel::class, ['order' => $this->order])
        ->set('order', Order::factory()->paid()->create()))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

it('shows the downloads once paid and empties the cart', function () {
    $this->order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);
    app(Cart::class)->add('track', $this->track->id, 70);
    expect(app(Cart::class)->count())->toBe(1);

    $this->withSession(['orders' => [$this->order->id]])->get($this->url)->assertOk()
        ->assertSee('Tack')->assertSee('name@example.se')
        ->assertSee('Order '.$this->order->number().' · 100 kr')
        ->assertSee('1 spår · WAV')->assertSee('5 av 5 kvar')->assertSee('Ladda ner WAV')
        ->assertSee(route('download', $this->item))
        ->assertDontSee('wire:poll', false);

    expect(app(Cart::class)->count())->toBe(0);
});

it('shows a preparing album without a link', function () {
    $release = Release::factory()->create(['artist' => 'S.D.L.', 'title' => 'Low End']);
    $this->order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);
    $album = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Release::class,
        'purchasable_id' => $release->id,
        'title' => 'Low End',
    ]);

    $this->withSession(['orders' => [$this->order->id]])->get($this->url)->assertOk()
        ->assertSee(__('shop.preparing'))->assertSee('Low End')
        ->assertDontSee(route('download', $album));
});

it('renders a row whose release was deleted from the title on the order', function () {
    $this->order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);
    $gone = OrderItem::factory()->for($this->order)->create([
        'purchasable_type' => Release::class,
        'purchasable_id' => 99999,
        'title' => 'Vanished',
    ]);

    $this->withSession(['orders' => [$this->order->id]])->get($this->url)->assertOk()
        ->assertSee('Vanished')->assertDontSee(route('download', $gone));
});

it('shows an exhausted item with a link to write', function () {
    $this->order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);
    $this->item->update(['download_count' => 5]);

    $this->withSession(['orders' => [$this->order->id]])->get($this->url)->assertOk()
        ->assertSee('Slut på nedladdningar')->assertSee('0 av 5 kvar')
        ->assertSee('href="mailto:patrik.sjeren@gmail.com"', false)
        ->assertDontSee(route('download', $this->item));
});
