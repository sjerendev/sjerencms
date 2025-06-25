<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SsrController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

// API routes should be handled by api.php
Route::prefix('api')->group(base_path('routes/api.php'));

// Contact form route
Route::post('/contact-form-submit', [ContactFormController::class, 'submit'])->name('contact.submit');

// Sitemap route
Route::get('sitemap.xml', [SitemapController::class, 'index']);

// Temporary debug route
Route::get('debug/posts', [SitemapController::class, 'debugPosts']);

// Test route for mail debugging
Route::get('/test-mail', function () {
    try {
        Mail::raw('Test email at ' . now(), function($message) {
            $message->to('info@kalibercreative.se')
                   ->subject('Test Email');
        });
        return 'Mail sent successfully!';
    } catch (\Exception $e) {
        return 'Mail error: ' . $e->getMessage();
    }
});

Route::middleware(['auth'])->group(function () {
    Route::get('/shop/download/{token}', [ShopController::class, 'download'])->name('shop.download');
});

// All other routes should be handled by the SPA with SSR
Route::get('/{any?}', function () {
    try {
        $ssr = json_decode(node(['url' => request()->getRequestUri()]));
        
        return view('app', [
            'ssr' => [
                'html' => $ssr->html ?? '',
                'head' => $ssr->head ?? '',
                'state' => $ssr->state ?? null,
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('SSR Error: ' . $e->getMessage());
        
        // Fallback to client-side rendering
        return view('app', [
            'ssr' => [
                'html' => '',
                'head' => '',
                'state' => null
            ]
        ]);
    }
})->where('any', '.*');
