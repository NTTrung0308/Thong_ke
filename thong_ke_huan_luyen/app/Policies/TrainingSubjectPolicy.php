<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingSubject;

class TrainingSubjectPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, TrainingSubject $trainingSubject)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan']);
    }

    public function update(User $user, TrainingSubject $trainingSubject)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan']);
    }

    public function delete(User $user, TrainingSubject $trainingSubject)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan']);
    }
}
