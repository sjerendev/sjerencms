<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Shop\LoginLinks;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * GET only shows a button, POST logs in. A mail link scanner sends GET, so it
 * can neither log in nor burn the link. Checks the signature itself instead
 * of the `signed` middleware, so an expired link ends on the login page.
 */
class ConfirmLogin
{
    public function __invoke(Request $request, LoginLinks $links, string $locale, string $user): View|RedirectResponse
    {
        $buyer = $request->hasValidSignature() ? User::find($user) : null;
        $nonce = (string) $request->query('nonce');

        // Decision 8: an admin never logs in with a link, even if one was made.
        if (! $buyer || $buyer->is_admin) {
            return $this->expired();
        }

        if ($request->isMethod('GET')) {
            if (! $links->valid($buyer, $nonce)) {
                return $this->expired();
            }

            return view('shop.login-confirm', [
                'title' => __('nav.login').' — Patrik Sjerén',
                'description' => __('meta.description'),
                'alternates' => [],
            ]);
        }

        if (! $links->consume($buyer, $nonce)) {
            return $this->expired();
        }

        Auth::login($buyer, remember: true);
        $request->session()->regenerate();

        return redirect()->route('shop.account');
    }

    private function expired(): RedirectResponse
    {
        return redirect()->route('shop.login')->with('expired', __('shop.expired'));
    }
}
