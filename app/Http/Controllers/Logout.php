<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();
        // Also drops session['orders'], which would otherwise still open downloads.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.login');
    }
}
