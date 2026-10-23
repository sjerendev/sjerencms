<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
    }
}
