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
        // 1. Nếu user có đơn vị cụ thể, kiểm tra theo cây đơn vị (chính xác nhất)
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();
            return in_array($soldier->unit_id, $allowedUnitIds);
        }
        
        // 2. Nếu user không có đơn vị (nhưng có Role), kiểm tra theo cấp bậc Role
        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $soldierLevelIndex = array_search($soldier->unit->level ?? 'trung-doi', $levelsHierarchy);
                
                // Nếu cấp của user cao hơn hoặc bằng cấp của quân nhân (Index nhỏ hơn hoặc bằng)
                return $userLevelIndex !== false && $soldierLevelIndex !== false && $userLevelIndex <= $soldierLevelIndex;
            }
        }

        return false;
    }
}
