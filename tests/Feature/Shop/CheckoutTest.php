<?php

use App\Enums\OrderStatus;
use App\Http\Middleware\SetLocaleFromPrefix;
use App\Livewire\CheckoutForm;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Track;
use App\Shop\Cart;
use App\Shop\StripeCheckout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Stripe\Exception\ApiConnectionException;

beforeEach(function () {
    Storage::fake('music');
    // Livewire::test skips the HTTP middleware that sets these.
    app()->setLocale('sv');
    URL::defaults(['locale' => 'se']);
    $this->track = Track::factory()->withWav()->create(['min_price' => 20, 'title' => 'Deep Down']);
});

it('sends an empty cart back to the music', fn () => $this->get('/se/kassa')->assertRedirect('/se/musik'));

it('shows the order next to the form', function () {
    app(Cart::class)->add('track', $this->track->id, 35);

    $this->get('/se/kassa')->assertOk()
        ->assertSee('Deep Down')->assertSee('Betala 35 kr', false)->assertSee('Nedladdning i WAV');
});

it('creates an unpaid order and sends the buyer to Stripe', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('start')->once()->andReturn('https://checkout.stripe.com/c/pay/test');
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)
        ->set('email', ' Name@Example.se ')->set('consent', true)
        ->call('pay')
        ->assertRedirect('https://checkout.stripe.com/c/pay/test');

    $order = Order::sole();
    expect($order->status)->toBe(OrderStatus::Unpaid)
        ->and($order->email)->toBe('name@example.se')
        ->and($order->total)->toBe(35)
        ->and($order->locale)->toBe('sv')
        ->and($order->consented_at)->not->toBeNull()
        ->and($order->items->sole()->only('title', 'amount', 'purchasable_type', 'purchasable_id'))
        ->toBe(['title' => 'Deep Down', 'amount' => 35, 'purchasable_type' => Track::class, 'purchasable_id' => $this->track->id])
        ->and(session('orders'))->toBe([$order->id]);
});

it('requires a valid email and consent', function () {
    $this->mock(StripeCheckout::class)->shouldNotReceive('start');
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)
        ->set('email', 'not-an-email')
        ->call('pay')
        ->assertHasErrors(['email', 'consent']);

    expect(Order::count())->toBe(0);
});

it('keeps an English buyer English', function () {
    app()->setLocale('en');
    URL::defaults(['locale' => 'en']);
    $this->mock(StripeCheckout::class)->shouldReceive('start')->once()->andReturn('https://checkout.stripe.com/c/pay/test');
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)->call('pay');

    $order = Order::sole();
    $payload = (new StripeCheckout)->payload($order);
    expect($order->locale)->toBe('en')
        ->and($payload['locale'])->toBe('en')
        ->and($payload['success_url'])->toEndWith("/en/kassa/tack/{$order->id}")
        ->and($payload['cancel_url'])->toEndWith('/en/kassa');
});

it('charges only what can still be bought', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('start')->once()->andReturn('https://checkout.stripe.com/c/pay/test');
    $other = Track::factory()->withWav()->create(['min_price' => 20, 'title' => 'Other']);
    app(Cart::class)->add('track', $this->track->id, 35);
    app(Cart::class)->add('track', $other->id, 40);
    $other->release->update(['is_published' => false]);

    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)->call('pay');

    $order = Order::sole();
    expect($order->total)->toBe(35)->and($order->items)->toHaveCount(1);
});

it('refuses a total under the Stripe minimum', function () {
    $this->mock(StripeCheckout::class)->shouldNotReceive('start');
    $free = Track::factory()->withWav()->create(['min_price' => 0]);
    app(Cart::class)->add('track', $free->id, 0);

    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)
        ->call('pay')
        ->assertHasErrors('pay');

    expect(Order::count())->toBe(0);
});

it('sends a buyer whose cart emptied back to the checkout', function () {
    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)
        ->call('pay')
        ->assertRedirect(route('shop.checkout'));

    expect(Order::count())->toBe(0);
});

it('shows an error and keeps the order unpaid when Stripe fails', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('start')->andThrow(new ApiConnectionException('down'));
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)
        ->call('pay')
        ->assertHasErrors('pay')->assertNoRedirect();

    expect(Order::sole()->status)->toBe(OrderStatus::Unpaid);
});

it('builds the Stripe payload from the order', function () {
    $order = Order::factory()->create(['locale' => 'sv']);
    OrderItem::factory()->for($order)->create(['title' => 'Album', 'amount' => 80]);

    $payload = app(StripeCheckout::class)->payload($order);

    expect($payload['mode'])->toBe('payment')
        ->and($payload['customer_email'])->toBe($order->email)
        ->and($payload['client_reference_id'])->toBe((string) $order->id)
        ->and($payload['metadata'])->toBe(['order_id' => (string) $order->id])
        ->and($payload['line_items'][0]['quantity'])->toBe(1)
        ->and($payload['line_items'][0]['price_data'])->toMatchArray(['currency' => 'sek', 'unit_amount' => 8000, 'tax_behavior' => 'inclusive'])
        ->and($payload['line_items'][0]['price_data']['product_data']['name'])->toBe('Album')
        ->and($payload['success_url'])->toEndWith("/se/kassa/tack/{$order->id}")
        ->and($payload['cancel_url'])->toEndWith('/se/kassa')
        ->and($payload['locale'])->toBe('sv')
        ->and($payload['automatic_tax'])->toBe(['enabled' => false])
        ->and($payload['adaptive_pricing'])->toBe(['enabled' => false])
        ->and($payload)->not->toHaveKey('payment_method_types');
});

it('shows the thanks page only to the browser that started the order', function () {
    $order = Order::factory()->create();

    $this->get("/se/kassa/tack/{$order->id}")->assertNotFound();
    $this->withSession(['orders' => [$order->id]])->get("/se/kassa/tack/{$order->id}")->assertOk()->assertSee('Tack');
});

it('maps languages to URL prefixes', function () {
    expect(SetLocaleFromPrefix::prefix('sv'))->toBe('se')
        ->and(SetLocaleFromPrefix::prefix('en'))->toBe('en')
        ->and(SetLocaleFromPrefix::alternates('kassa'))
        ->toBe(['sv' => url('/se/kassa'), 'en' => url('/en/kassa')]);
});

it('leaves the checkout when the drawer empties the cart', function () {
    Livewire::test(CheckoutForm::class)->dispatch('cart-updated', count: 0)->assertRedirect(route('music.index'));
});

it('shows an error instead of crashing when no Stripe key is set', function () {
    config(['services.stripe.secret' => null]);
    app(Cart::class)->add('track', $this->track->id, 35);

    Livewire::test(CheckoutForm::class)->set('email', 'a@b.se')->set('consent', true)
        ->call('pay')
        ->assertHasErrors('pay')->assertNoRedirect();

    expect(Order::sole()->status)->toBe(OrderStatus::Unpaid);
});
