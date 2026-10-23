<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Mail\OrderPaid;
use App\Models\Order;
use App\Models\User;
use App\Shop\LoginLinks;
use App\Shop\StripeCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Stripe\StripeObject;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

/**
 * The only place an order becomes paid. Signed by Stripe, idempotent, and
 * answers 200 to anything valid so Stripe does not resend it.
 */
class StripeWebhook extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = (string) config('services.stripe.webhook_secret');
        // An empty secret would let anyone sign an event. Report it (HttpExceptions are not), and keep the cause out of the response.
        if ($secret === '') {
            report(new RuntimeException('Stripe webhook secret is not configured.'));
            abort(500);
        }

        try {
            $event = Webhook::constructEvent($request->getContent(), (string) $request->header('Stripe-Signature'), $secret);
        } catch (SignatureVerificationException|UnexpectedValueException) {
            return response('', 400);
        }

        $object = $event->data->object;

        match (true) {
            $event->type === 'checkout.session.completed' && $object->payment_status === 'paid',
            $event->type === 'checkout.session.async_payment_succeeded' => $this->paid($object),
            $event->type === 'charge.refunded' && $object->refunded === true => $this->refunded($object),
            default => null,
        };

        return response('');
    }

    private function paid(StripeObject $session): void
    {
        $order = Order::with('items')->find($session->client_reference_id);

        if (! $order
            || $order->stripe_session_id !== $session->id
            || $session->amount_total !== $order->total * 100
            || $session->currency !== 'sek') {
            // The buyer may have been charged: this must reach the error channel.
            Log::error('Stripe session does not match its order, left unpaid.', [
                'session' => $session->id,
                'order' => $session->client_reference_id,
                'amount' => $session->amount_total,
                'currency' => $session->currency,
            ]);

            return;
        }

        $user = DB::transaction(function () use ($order, $session) {
            $changed = Order::whereKey($order->id)->where('status', OrderStatus::Unpaid)->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
                'country' => $session->customer_details->address->country ?? null,
                'stripe_payment_intent' => $session->payment_intent ?? null,
            ]);

            if ($changed === 0) {
                return null; // A repeat of an event already handled.
            }

            // Not firstOrCreate(['email' => ...]): SQLite compares case-sensitively, MySQL does not.
            $email = Str::lower(trim($order->email));
            $user = User::whereRaw('lower(email) = ?', [$email])->first()
                ?? User::create(['name' => $email, 'email' => $email, 'password' => Str::random(40)]);
            Order::whereKey($order->id)->update(['user_id' => $user->id]);

            return $user;
        });

        if (! $user) {
            return;
        }

        // Paid is committed. Whatever fails from here is reported, never retried by Stripe.
        $order->refresh();

        try {
            if ($url = app(StripeCheckout::class)->receiptUrl($order->stripe_payment_intent)) {
                $order->update(['receipt_url' => $url]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            $loginUrl = $user->is_admin ? null : app(LoginLinks::class)->url($user, $order->locale);
            Mail::to($order->email)->locale($order->locale)->send(new OrderPaid($order, $loginUrl));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function refunded(StripeObject $charge): void
    {
        if ($charge->payment_intent) {
            Order::paid()->where('stripe_payment_intent', $charge->payment_intent)->update(['status' => OrderStatus::Refunded]);
        }
    }
}
