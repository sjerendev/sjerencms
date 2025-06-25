<?php

use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/pages', [PageController::class, 'index']);
Route::get('/navigation', [NavigationController::class, 'index']);
Route::get('/navigation/footer', [NavigationController::class, 'footer']);
Route::get('/pages/home', [PageController::class, 'home']);
Route::get('/pages/{slug}', [PageController::class, 'show']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);
Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/create-payment-intent', [App\Http\Controllers\ShopController::class, 'createPaymentIntent']);
    Route::get('/downloads', [App\Http\Controllers\ShopController::class, 'getDownloads']);
});

Route::name('api.')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/categories', [ProductCategoryController::class, 'index'])->name('categories.index');
});
