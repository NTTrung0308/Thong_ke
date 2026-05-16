<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeaponEquipment;

class WeaponEquipmentPolicy
{
    public function view(User $user, WeaponEquipment $weapon)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $weapon);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, WeaponEquipment $weapon)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $weapon);
    }

    public function delete(User $user, WeaponEquipment $weapon)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $weapon);
    }

    private function isInSameOrSubUnit(User $user, WeaponEquipment $weapon)
    {
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();
            return in_array($weapon->unit_id, $allowedUnitIds);
        }
        
        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $targetLevel = $weapon->unit->level ?? 'trung-doi';
                $targetLevelIndex = array_search($targetLevel, $levelsHierarchy);
                
                return $userLevelIndex !== false && $targetLevelIndex !== false && $userLevelIndex <= $targetLevelIndex;
            }
        }

        return false;
    }
}
