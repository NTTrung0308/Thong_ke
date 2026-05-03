<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingResult;

class TrainingResultPolicy
{
    public function view(User $user, TrainingResult $trainingResult)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $trainingResult);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, TrainingResult $trainingResult)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $trainingResult);
    }

    public function delete(User $user, TrainingResult $trainingResult)
    {
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, TrainingResult $trainingResult)
    {
        return $user->unit_id === $trainingResult->unit_id ||
               $user->unit->children()->where('id', $trainingResult->unit_id)->exists();
    }
}
