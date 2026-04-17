<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Soldier;

class SoldierPolicy
{
    public function view(User $user, Soldier $soldier)
    {
        if ($user->hasRole('chi-huy')) return true;

        // Kiểm tra user có thuộc cùng đơn vị hoặc đơn vị cấp dưới không
        return $this->isInSameOrSubUnit($user, $soldier);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, Soldier $soldier)
    {
        if ($user->hasRole('chi-huy')) return true;

        return $this->isInSameOrSubUnit($user, $soldier);
    }

    public function delete(User $user, Soldier $soldier)
    {
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, Soldier $soldier)
    {
        // Logic kiểm tra đơn vị
        return $user->unit_id === $soldier->unit_id ||
               $user->unit->children()->where('id', $soldier->unit_id)->exists();
    }
}
