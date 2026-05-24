<?php

namespace App\Providers;

use App\Models\Soldier;
use App\Models\WeaponEquipment;
use App\Models\Reward;
use App\Models\Discipline;
use App\Models\TrainingResult;
use App\Models\TrainingLog;
use App\Models\TrainingSubject;
use App\Policies\SoldierPolicy;
use App\Policies\WeaponEquipmentPolicy;
use App\Policies\RewardPolicy;
use App\Policies\DisciplinePolicy;
use App\Policies\TrainingResultPolicy;
use App\Policies\TrainingLogPolicy;
use App\Policies\TrainingSubjectPolicy;
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
        TrainingLog::class => TrainingLogPolicy::class,
        TrainingSubject::class => TrainingSubjectPolicy::class,
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
