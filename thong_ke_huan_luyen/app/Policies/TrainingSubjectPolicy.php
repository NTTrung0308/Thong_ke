<?php

namespace App\Policies;

use App\Models\TrainingSubject;
use App\Models\User;

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
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function update(User $user, TrainingSubject $trainingSubject)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }

    public function delete(User $user, TrainingSubject $trainingSubject)
    {
        return $user->hasAnyRole(['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi']);
    }
}
