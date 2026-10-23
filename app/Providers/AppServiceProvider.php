<?php

namespace App\Providers;

use App\Http\Middleware\SetLocaleFromPrefix;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Livewire reloads the page instead of navigating when a tracked asset changed.
        Vite::useScriptTagAttributes(['data-navigate-track' => true]);
        Vite::useStyleTagAttributes(['data-navigate-track' => true]);

        // Livewire posts to its own endpoint, outside {locale}. Without this the
        // language falls back to sv and route() lacks its `locale` parameter.
        Livewire::addPersistentMiddleware([SetLocaleFromPrefix::class]);
    }
}
