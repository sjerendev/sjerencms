<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;

/** MinaKop.dc.html. Only paid orders show here; a refunded one no longer has downloads. */
class ShowAccount
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $orders = $user->orders()->paid()
            ->with(['items' => fn ($items) => $items->withPurchasable()])
            ->latest('paid_at')->latest('id')
            ->get();

        return view('shop.account', [
            'title' => __('nav.account').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates('mina-kop'),
            'user' => $user,
            'orders' => $orders,
            'items' => $orders->flatMap->items,
            'limit' => app(GeneralSettings::class)->download_limit,
        ]);
    }
}
