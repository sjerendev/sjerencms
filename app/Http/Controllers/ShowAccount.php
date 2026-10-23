<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use Illuminate\Contracts\View\View;

/** Skeleton: Task 8 fills in the downloads and receipts. */
class ShowAccount
{
    public function __invoke(): View
    {
        return view('shop.account', [
            'title' => __('nav.account').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates('mina-kop'),
            'user' => auth()->user(),
        ]);
    }
}
