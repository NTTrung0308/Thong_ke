<?php

namespace App\Providers;

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
        \App\Models\Soldier::observe(\App\Observers\SoldierObserver::class);
        \App\Models\Unit::observe(\App\Observers\UnitObserver::class);
    }
}
