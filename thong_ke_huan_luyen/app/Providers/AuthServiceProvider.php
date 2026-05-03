<?php

namespace App\Providers;

use App\Models\Soldier;
use App\Policies\SoldierPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Soldier::class => SoldierPolicy::class,
        WeaponEquipment::class => WeaponEquipmentPolicy::class,
        Reward::class => RewardPolicy::class,
        Discipline::class => DisciplinePolicy::class,
        TrainingResult::class => TrainingResultPolicy::class,
        Training::class => TrainingPolicy::class,
        TrainingLog::class => TrainingLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
