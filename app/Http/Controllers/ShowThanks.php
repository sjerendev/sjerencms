<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Order;
use App\Shop\Cart;
use Illuminate\Contracts\View\View;

/** Only the browser that started the order, or its owner, may see it. The live part is the OrderStatusPanel. */
class ShowThanks
{
    public function __invoke(string $locale, Order $order): View
    {
        abort_unless(
            in_array($order->id, session('orders', []), true)
            || (auth()->id() && auth()->id() === $order->user_id),
            404,
        );
        abort_if($order->status === OrderStatus::Refunded, 404);

        app(Cart::class)->clear();

        return view('shop.thanks', [
            'title' => __('shop.thanks').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates("kassa/tack/{$order->id}"),
            'order' => $order,
        ]);
    }
}
