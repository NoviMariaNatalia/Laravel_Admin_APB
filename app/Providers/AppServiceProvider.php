<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BookingFirebaseService;
use App\Services\SimpleFirebaseService;
use App\Services\RoomFirebaseService; // Tambahan untuk Room

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Firebase Booking Services
        $this->app->singleton(BookingFirebaseService::class, function ($app) {
            return new BookingFirebaseService();
        });

        $this->app->singleton(SimpleFirebaseService::class, function ($app) {
            return new SimpleFirebaseService();
        });

        // Register Firebase Room Service
        $this->app->singleton(RoomFirebaseService::class, function ($app) {
            return new RoomFirebaseService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}