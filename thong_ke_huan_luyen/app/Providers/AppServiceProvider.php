<?php

namespace App\Providers;

use App\Models\Soldier;
use App\Models\Unit;
use App\Observers\SoldierObserver;
use App\Observers\UnitObserver;
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
        Soldier::observe(SoldierObserver::class);
        Unit::observe(UnitObserver::class);
    }
}
