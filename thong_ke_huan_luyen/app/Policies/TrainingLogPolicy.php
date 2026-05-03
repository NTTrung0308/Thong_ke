<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingLog;

class TrainingLogPolicy
{
    public function view(User $user, TrainingLog $log)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $log);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, TrainingLog $log)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $log);
    }

    public function delete(User $user, TrainingLog $log)
    {
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, TrainingLog $log)
    {
        return $user->unit_id === $log->unit_id ||
               $user->unit->children()->where('id', $log->unit_id)->exists();
    }
}
