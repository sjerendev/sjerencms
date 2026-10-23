<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Shop\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShowCheckout
{
    public function __invoke(Cart $cart): View|RedirectResponse
    {
        if ($cart->count() === 0) {
            return redirect()->route('music.index');
        }

        return view('shop.checkout', [
            'title' => __('shop.checkout').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates('kassa'),
        ]);
    }
}
