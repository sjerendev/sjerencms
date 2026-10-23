<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Models\Order;
use Illuminate\Contracts\View\View;

/** Stub: Task 8 fills in the page. Only the browser that started the order, or its owner, may see it. */
class ShowThanks
{
    public function __invoke(string $locale, Order $order): View
    {
        abort_unless(
            in_array($order->id, session('orders', []))
            || (auth()->id() && auth()->id() === $order->user_id),
            404,
        );

        return view('shop.thanks', [
            'title' => __('shop.thanks').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates("kassa/tack/{$order->id}"),
        ]);
    }
}
