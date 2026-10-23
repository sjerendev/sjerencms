<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Login links are built from the request host: only APP_URL and its subdomains may be it.
        $middleware->trustHosts();
        // Stripe posts here; the webhook's signature is its authentication.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
        // /admin has Filament's own login. The shop's guarded routes sit under {locale}.
        $middleware->redirectGuestsTo(fn (Request $request) => route('shop.login', ['locale' => $request->route('locale')]));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
