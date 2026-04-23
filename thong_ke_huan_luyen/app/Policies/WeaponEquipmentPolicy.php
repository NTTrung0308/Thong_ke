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
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, WeaponEquipment $weapon)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $weapon);
    }

    public function delete(User $user, WeaponEquipment $weapon)
    {
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, WeaponEquipment $weapon)
    {
        return $user->unit_id === $weapon->unit_id ||
               $user->unit->children()->where('id', $weapon->unit_id)->exists();
    }
}
