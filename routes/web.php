<?php

use App\Http\Controllers\ConfirmLogin;
use App\Http\Controllers\DownloadPurchase;
use App\Http\Controllers\Logout;
use App\Http\Controllers\ShowAccount;
use App\Http\Controllers\ShowCheckout;
use App\Http\Controllers\ShowLogin;
use App\Http\Controllers\ShowMusic;
use App\Http\Controllers\ShowPage;
use App\Http\Controllers\ShowRelease;
use App\Http\Controllers\ShowThanks;
use App\Http\Controllers\StripeWebhook;
use App\Http\Middleware\SetLocaleFromPrefix;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/se');
// Called by Stripe, so outside the locale group and without CSRF (bootstrap/app.php).
Route::post('stripe/webhook', StripeWebhook::class)->name('stripe.webhook');
// Owner or paying browser only, and not under a locale prefix: the e-mail links to it.
Route::get('download/{item}', DownloadPurchase::class)->name('download');
Route::permanentRedirect('en/about-me', '/en/om-mig');
Route::permanentRedirect('en/projects', '/en/projekt');
// Decision 9: targets that never existed go to the home page.
Route::permanentRedirect('en/services', '/en');
Route::permanentRedirect('en/reputation', '/en');

Route::prefix('{locale}')->whereIn('locale', array_keys(SetLocaleFromPrefix::LOCALES))
    ->middleware(SetLocaleFromPrefix::class)->group(function () {
        Route::get('/', ShowPage::class)->name('page.home');
        // Before {slug}, or the page router takes /musik.
        Route::get('musik', ShowMusic::class)->name('music.index');
        Route::get('musik/{release:slug}', ShowRelease::class)->name('music.show');
        Route::get('kassa', ShowCheckout::class)->name('shop.checkout');
        Route::get('kassa/tack/{order}', ShowThanks::class)->name('shop.thanks');
        Route::get('logga-in', ShowLogin::class)->name('shop.login');
        Route::match(['get', 'post'], 'logga-in/{user}', ConfirmLogin::class)->name('shop.login.confirm');
        Route::post('logga-ut', Logout::class)->name('shop.logout');
        Route::get('mina-kop', ShowAccount::class)->middleware('auth')->name('shop.account');
        Route::get('{slug}', ShowPage::class)->where('slug', '[a-z0-9-]+')->name('page.show');
    });
