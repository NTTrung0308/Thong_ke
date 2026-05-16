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
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, TrainingResult $trainingResult)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $trainingResult);
    }

    public function delete(User $user, TrainingResult $trainingResult)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $trainingResult);
    }

    private function isInSameOrSubUnit(User $user, TrainingResult $trainingResult)
    {
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();
            return in_array($trainingResult->unit_id, $allowedUnitIds);
        }
        
        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $targetLevel = $trainingResult->unit->level ?? 'trung-doi';
                $targetLevelIndex = array_search($targetLevel, $levelsHierarchy);
                
                return $userLevelIndex !== false && $targetLevelIndex !== false && $userLevelIndex <= $targetLevelIndex;
            }
        }

        return false;
    }
}
