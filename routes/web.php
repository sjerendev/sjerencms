<?php

use App\Http\Controllers\ShowMusic;
use App\Http\Controllers\ShowPage;
use App\Http\Controllers\ShowRelease;
use App\Http\Middleware\SetLocaleFromPrefix;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/se');
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
        // Placeholder so the cart can link here; Task 4 replaces it with the checkout.
        Route::get('kassa', fn () => abort(404))->name('shop.checkout');
        Route::get('{slug}', ShowPage::class)->where('slug', '[a-z0-9-]+')->name('page.show');
    });
