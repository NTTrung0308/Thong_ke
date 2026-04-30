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
        // Đại đội trưởng trở lên mới được tạo kỷ luật
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi']);
    }

    public function update(User $user, Discipline $discipline)
    {
        if ($user->hasRole('chi-huy')) return true;

        // Chỉ người cùng đơn vị hoặc cấp trên mới sửa
        return $this->isInSameOrSubUnit($user, $discipline);
    }

    public function delete(User $user, Discipline $discipline)
    {
        // Chỉ chỉ huy mới được xóa kỷ luật
        return $user->hasRole('chi-huy');
    }

    private function isInSameOrSubUnit(User $user, Discipline $discipline)
    {
        return $user->unit_id === $discipline->unit_id ||
               $user->unit->children()->where('id', $discipline->unit_id)->exists();
    }
}
