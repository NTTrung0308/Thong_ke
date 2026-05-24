<?php

namespace App\Policies;

use App\Models\Reward;
use App\Models\User;

class RewardPolicy
{
    public function view(User $user, Reward $reward)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        return $this->isInSameOrSubUnit($user, $reward);
    }

    public function create(User $user)
    {
        // Các cấp đơn vị đều được tạo khen thưởng cho đơn vị mình
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, Reward $reward)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        // Chỉ người cùng đơn vị hoặc cấp trên mới sửa được
        return $this->isInSameOrSubUnit($user, $reward);
    }

    public function delete(User $user, Reward $reward)
    {
        if ($user->hasRole('chi-huy')) {
            return true;
        }

        // Cho phép xóa khen thưởng của đơn vị mình hoặc cấp dưới
        return $this->isInSameOrSubUnit($user, $reward);
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
