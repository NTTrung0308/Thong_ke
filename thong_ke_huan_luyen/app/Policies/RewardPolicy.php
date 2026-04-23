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
        return $user->unit_id === $reward->unit_id ||
               $user->unit->children()->where('id', $reward->unit_id)->exists();
    }
}
