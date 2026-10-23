<?php

namespace App\Shop;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Order;
use Stripe\StripeClient;

/**
 * Hands an order to Stripe's hosted Checkout. The client is built when it is
 * needed, so the container can make this class without a key.
 */
class StripeCheckout
{
    /**
     * What Checkout needs, built from the order alone. The URLs carry the
     * order's own language: this may run outside the locale group.
     *
     * @return array<string, mixed>
     */
    public function payload(Order $order): array
    {
        $locale = ['locale' => SetLocaleFromPrefix::prefix($order->locale)];

        return [
            'mode' => 'payment',
            'customer_email' => $order->email,
            'client_reference_id' => (string) $order->id,
            'locale' => $order->locale,
            'line_items' => $order->items->map(fn ($item) => [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'sek',
                    'unit_amount' => $item->amount * 100,
                    'tax_behavior' => 'inclusive',
                    'product_data' => ['name' => $item->title],
                ],
            ])->all(),
            'automatic_tax' => ['enabled' => config('services.stripe.tax')],
            'metadata' => ['order_id' => (string) $order->id],
            'success_url' => route('shop.thanks', $locale + ['order' => $order->id]),
            'cancel_url' => route('shop.checkout', $locale),
        ];
    }

    /** Creates the Checkout session, remembers it on the order and returns where to send the buyer. */
    public function start(Order $order): string
    {
        $session = $this->client()->checkout->sessions->create($this->payload($order));

        $order->update(['stripe_session_id' => $session->id]);

        return $session->url;
    }

    /** Stripe's own receipt page for a payment, if the charge has one. */
    public function receiptUrl(string $paymentIntent): ?string
    {
        $intent = $this->client()->paymentIntents->retrieve($paymentIntent, ['expand' => ['latest_charge']]);

        return $intent->latest_charge->receipt_url ?? null;
    }

    private function client(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }
}
