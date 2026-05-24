<?php

namespace App\Policies;

use App\Models\TrainingLog;
use App\Models\User;

class TrainingLogPolicy
{
    public function view(User $user, TrainingLog $log)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        return $this->isInSameOrSubUnit($user, $log);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, TrainingLog $log)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        return $this->isInSameOrSubUnit($user, $log);
    }

    public function delete(User $user, TrainingLog $log)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        return $this->isInSameOrSubUnit($user, $log);
    }

    private function isInSameOrSubUnit(User $user, TrainingLog $log)
    {
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();

            return in_array($log->unit_id, $allowedUnitIds);
        }

        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $targetLevel = $log->unit->level ?? 'trung-doi';
                $targetLevelIndex = array_search($targetLevel, $levelsHierarchy);

                return $userLevelIndex !== false && $targetLevelIndex !== false && $userLevelIndex <= $targetLevelIndex;
            }
        }

        return false;
    }
}
