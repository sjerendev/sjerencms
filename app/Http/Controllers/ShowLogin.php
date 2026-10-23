<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPrefix;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShowLogin
{
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('shop.account');
        }

        return view('shop.login', [
            'title' => __('nav.login').' — Patrik Sjerén',
            'description' => __('meta.description'),
            'alternates' => SetLocaleFromPrefix::alternates('logga-in'),
        ]);
    }
}
