<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reward;

class RewardPolicy
{
    public function view(User $user, Reward $reward)
    {
        if ($user->hasRole('chi-huy')) return true;
        return $this->isInSameOrSubUnit($user, $reward);
    }

    public function create(User $user)
    {
        // Đại đội trưởng trở lên mới được tạo khen thưởng
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, Reward $reward)
    {
        if ($user->hasRole('chi-huy')) return true;
        // Chỉ người cùng đơn vị hoặc cấp trên mới sửa được
        return $this->isInSameOrSubUnit($user, $reward);
    }

    public function delete(User $user, Reward $reward)
    {
        // Chỉ chỉ huy mới được xóa
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, Reward $reward)
    {
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();
            return in_array($reward->unit_id, $allowedUnitIds);
        }
        
        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $targetLevel = $reward->unit->level ?? 'trung-doi';
                $targetLevelIndex = array_search($targetLevel, $levelsHierarchy);
                
                return $userLevelIndex !== false && $targetLevelIndex !== false && $userLevelIndex <= $targetLevelIndex;
            }
        }

        return false;
    }
}
