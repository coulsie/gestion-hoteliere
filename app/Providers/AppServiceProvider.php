<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <-- TRÈS IMPORTANT

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
        // Force la longueur par défaut des index de chaînes à 191 caractères
        Schema::defaultStringLength(191);
        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);

    }
}

