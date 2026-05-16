<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Discipline;

class DisciplinePolicy
{
    public function view(User $user, Discipline $discipline)
    {
        if ($user->hasRole('chi-huy')) return true;

        // Chỉ được xem kỷ luật của đơn vị mình hoặc cấp dưới
        return $this->isInSameOrSubUnit($user, $discipline);
    }

    public function create(User $user)
    {
        // Các cấp đơn vị đều được tạo kỷ luật cho đơn vị mình
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, Discipline $discipline)
    {
        if ($user->hasRole('chi-huy')) return true;

        // Chỉ người cùng đơn vị hoặc cấp trên mới sửa
        return $this->isInSameOrSubUnit($user, $discipline);
    }

    public function delete(User $user, Discipline $discipline)
    {
        if ($user->hasRole('chi-huy')) return true;

        // Cho phép xóa kỷ luật của đơn vị mình hoặc cấp dưới
        return $this->isInSameOrSubUnit($user, $discipline);
    }

    private function isInSameOrSubUnit(User $user, Discipline $discipline)
    {
        // 1. Nếu user có đơn vị cụ thể, kiểm tra theo cây đơn vị
        if ($user->unit) {
            $allowedUnitIds = $user->unit->getAllDescendantIds();
            return in_array($discipline->unit_id, $allowedUnitIds);
        }
        
        // 2. Nếu user không có đơn vị (nhưng có Role), kiểm tra theo cấp bậc Role
        $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        foreach ($levels as $l) {
            if ($user->hasRole($l)) {
                $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                $userLevelIndex = array_search($l, $levelsHierarchy);
                $targetLevel = $discipline->unit->level ?? 'trung-doi';
                $targetLevelIndex = array_search($targetLevel, $levelsHierarchy);
                
                return $userLevelIndex !== false && $targetLevelIndex !== false && $userLevelIndex <= $targetLevelIndex;
            }
        }

        return false;
    }
}
