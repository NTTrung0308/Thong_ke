<?php

namespace App\Observers;

use App\Models\TrainingSubject;

class TrainingSubjectObserver
{
    /**
     * Handle the TrainingSubject "deleting" event.
     */
    public function deleting(TrainingSubject $trainingSubject): void
    {
        // Xóa mềm các môn học/nội dung con
        foreach ($trainingSubject->children as $child) {
            $child->delete();
        }
    }

    /**
     * Handle the TrainingSubject "restoring" event.
     */
    public function restoring(TrainingSubject $trainingSubject): void
    {
        // Khôi phục các môn học/nội dung con bị xóa cùng lúc
        $children = TrainingSubject::onlyTrashed()
            ->where('parent_id', $trainingSubject->id)
            ->where('deleted_at', '>=', $trainingSubject->deleted_at)
            ->get();

        foreach ($children as $child) {
            $child->restore();
        }
    }
}
