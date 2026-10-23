<?php

use App\Enums\OrderStatus;
use App\Mail\OrderPaid;
use App\Models\Order;
use App\Models\Track;
use App\Models\User;
use App\Shop\StripeCheckout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/** Signs a real payload, so it goes through Webhook::constructEvent. */
function stripeEvent(string $type, array $object, string $secret = 'whsec_test'): TestResponse
{
    config(['services.stripe.webhook_secret' => 'whsec_test']);
    $payload = json_encode(['id' => 'evt_'.Str::random(8), 'object' => 'event', 'type' => $type, 'data' => ['object' => $object]]);
    $t = time();
    $signature = "t={$t},v1=".hash_hmac('sha256', "{$t}.{$payload}", $secret);

    return test()->call('POST', '/stripe/webhook', server: ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], content: $payload);
}

function checkoutSession(Order $order, array $overrides = []): array
{
    return array_replace_recursive([
        'id' => $order->stripe_session_id, 'object' => 'checkout.session',
        'client_reference_id' => (string) $order->id, 'payment_status' => 'paid',
        'amount_total' => $order->total * 100, 'currency' => 'sek', 'payment_intent' => 'pi_test',
        'customer_details' => ['address' => ['country' => 'SE']],
    ], $overrides);
}

beforeEach(function () {
    Mail::fake();
    $this->mock(StripeCheckout::class)->shouldReceive('receiptUrl')->andReturn('https://pay.stripe.com/receipts/x');
    $this->order = Order::factory()->create(['stripe_session_id' => 'cs_test', 'total' => 100, 'email' => 'name@example.se']);
});

it('rejects a bad signature', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order), secret: 'whsec_wrong')->assertStatus(400);

    expect($this->order->fresh()->status)->toBe(OrderStatus::Unpaid);
    Mail::assertNothingSent();
});

it('rejects a request without a signature', function () {
    config(['services.stripe.webhook_secret' => 'whsec_test']);

    $this->call('POST', '/stripe/webhook', server: ['CONTENT_TYPE' => 'application/json'], content: '{}')->assertStatus(400);
});

it('refuses everything while no webhook secret is configured', function () {
    config(['services.stripe.webhook_secret' => null]);

    $this->call('POST', '/stripe/webhook', server: ['HTTP_STRIPE_SIGNATURE' => 't=1,v1=x'], content: '{}')->assertStatus(500);
});

it('marks the order paid, creates the buyer and mails the receipt', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    $order = $this->order->fresh();
    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->paid_at)->not->toBeNull()
        ->and($order->country)->toBe('SE')
        ->and($order->stripe_payment_intent)->toBe('pi_test')
        ->and($order->receipt_url)->toBe('https://pay.stripe.com/receipts/x')
        ->and($order->user->email)->toBe('name@example.se');
    Mail::assertSent(OrderPaid::class, fn ($mail) => $mail->hasTo('name@example.se')
        && str_contains($mail->loginUrl, '/se/logga-in/'));
});

it('does nothing the second time', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();
    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    expect(User::count())->toBe(1);
    Mail::assertSentCount(1);
});

it('waits for a delayed payment', function () {
    stripeEvent('checkout.session.completed', checkoutSession($this->order, ['payment_status' => 'unpaid']))->assertOk();
    expect($this->order->fresh()->status)->toBe(OrderStatus::Unpaid);
    Mail::assertNothingSent();

    stripeEvent('checkout.session.async_payment_succeeded', checkoutSession($this->order))->assertOk();
    expect($this->order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('never pays an order whose amount, currency or session does not match', function ($overrides) {
    stripeEvent('checkout.session.completed', checkoutSession($this->order, $overrides))->assertOk();

    expect($this->order->fresh()->status)->toBe(OrderStatus::Unpaid)
        ->and(User::count())->toBe(0);
    Mail::assertNothingSent();
})->with([[['amount_total' => 100]], [['currency' => 'eur']], [['id' => 'cs_other']], [['client_reference_id' => '999']]]);

it('reuses an existing buyer whatever the case of the address', function () {
    $buyer = User::factory()->create(['email' => 'Name@Example.se']);

    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    expect(User::count())->toBe(1)
        ->and($this->order->fresh()->user_id)->toBe($buyer->id);
});

it('stores a new buyer with the lowercased address', function () {
    $order = Order::factory()->create(['stripe_session_id' => 'cs_mixed', 'total' => 50, 'email' => 'Mixed@Example.se']);

    stripeEvent('checkout.session.completed', checkoutSession($order))->assertOk();

    expect($order->fresh()->user->email)->toBe('mixed@example.se');
});

it('marks a fully refunded order refunded', function () {
    $paid = Order::factory()->paid()->create(['stripe_payment_intent' => 'pi_test']);

    stripeEvent('charge.refunded', ['object' => 'charge', 'payment_intent' => 'pi_test', 'refunded' => false])->assertOk();
    expect($paid->fresh()->status)->toBe(OrderStatus::Paid);

    stripeEvent('charge.refunded', ['object' => 'charge', 'payment_intent' => 'pi_test', 'refunded' => true])->assertOk();
    expect($paid->fresh()->status)->toBe(OrderStatus::Refunded);
});

it('answers 200 to events it does not handle', function () {
    stripeEvent('customer.created', ['object' => 'customer'])->assertOk();
});

it('stays paid and answers 200 when the mail fails', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));

    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    expect($this->order->fresh()->status)->toBe(OrderStatus::Paid);
});

it('stays paid, keeps the mail and answers 200 when the receipt lookup fails', function () {
    $this->mock(StripeCheckout::class)->shouldReceive('receiptUrl')->andThrow(new RuntimeException('stripe down'));

    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    $order = $this->order->fresh();
    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->receipt_url)->toBeNull();
    Mail::assertSent(OrderPaid::class);
});

it('mails an admin buyer without a login link', function () {
    User::factory()->admin()->create(['email' => 'name@example.se']);

    stripeEvent('checkout.session.completed', checkoutSession($this->order))->assertOk();

    Mail::assertSent(OrderPaid::class, fn ($mail) => $mail->hasTo('name@example.se') && $mail->loginUrl === null);
});

it('renders the receipt with its lines, the total, the download button and the receipt link', function () {
    $this->order->items()->create(['purchasable_type' => Track::class, 'purchasable_id' => 1, 'title' => 'Night Drive', 'amount' => 100]);
    $this->order->update(['receipt_url' => 'https://pay.stripe.com/receipts/x']);

    $html = (new OrderPaid($this->order->fresh(), 'https://example.test/se/logga-in/1'))->render();
    expect($html)->toContain('Night Drive', '100 kr', 'https://example.test/se/logga-in/1', 'https://pay.stripe.com/receipts/x');

    expect((new OrderPaid($this->order->fresh(), null))->render())->not->toContain('logga-in');
});
