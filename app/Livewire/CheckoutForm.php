<?php

namespace App\Livewire;

use App\Models\Order;
use App\Shop\Cart;
use App\Shop\StripeCheckout;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;

/**
 * Kassa.dc.html. pay() reads the cart again, so the amount charged is never
 * what the browser last showed. Only the Stripe webhook marks an order paid.
 */
class CheckoutForm extends Component
{
    public string $email = '';

    public bool $consent = false;

    /** The drawer can change the cart from this page. */
    #[On('cart-updated')]
    public function refresh(): void
    {
        if (app(Cart::class)->count() === 0) {
            $this->redirectRoute('music.index');
        }
    }

    public function pay(StripeCheckout $stripe): void
    {
        $this->email = Str::lower(trim($this->email));

        $invalid = __('shop.emailInvalid');
        $this->validate(
            ['email' => 'required|email|max:255', 'consent' => 'accepted'],
            ['email.required' => $invalid, 'email.email' => $invalid, 'email.max' => $invalid, 'consent.accepted' => __('shop.consentRequired')],
        );

        $lines = app(Cart::class)->lines();

        if ($lines->isEmpty()) {
            $this->redirectRoute('shop.checkout');

            return;
        }

        if ($lines->sum('amount') < Cart::MIN_TOTAL) {
            $this->addError('pay', __('shop.belowMinimum'));

            return;
        }

        $order = DB::transaction(function () use ($lines) {
            $order = Order::create([
                'email' => $this->email,
                'total' => $lines->sum('amount'),
                'locale' => app()->getLocale(),
                'consented_at' => now(),
            ]);

            $order->items()->createMany($lines->map(fn ($line) => [
                'purchasable_type' => $line->item::class,
                'purchasable_id' => $line->item->id,
                'title' => $line->item->title,
                'amount' => $line->amount,
            ])->all());

            return $order;
        });

        session()->push('orders', $order->id);

        try {
            $this->redirect($stripe->start($order));
        } catch (ApiErrorException $e) {
            report($e);
            $this->addError('pay', __('shop.payFailed'));
        }
    }

    public function render(): View
    {
        $lines = app(Cart::class)->lines();

        return view('livewire.checkout-form', ['lines' => $lines, 'total' => $lines->sum('amount')]);
    }
}
