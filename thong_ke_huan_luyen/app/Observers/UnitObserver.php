<?php

namespace App\Observers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UnitObserver
{
    /**
     * Handle the Unit "saved" event.
     */
    public function saved(Unit $unit): void
    {
        $this->clearUserUnitCache();
    }

    /**
     * Handle the Unit "deleted" event.
     */
    public function deleted(Unit $unit): void
    {
        $this->clearUserUnitCache();
    }

    /**
     * Handle the Unit "deleting" event to cascade soft deletes.
     */
    public function deleting(Unit $unit): void
    {
        // Sử dụng static variable để tránh vòng lặp vô hạn nếu có cycle
        static $deletingIds = [];
        
        if (in_array($unit->id, $deletingIds)) {
            return;
        }
        $deletingIds[] = $unit->id;

        // 1. Xóa mềm các đơn vị con
        foreach ($unit->children as $child) {
            $child->delete();
        }

        // 2. Xóa mềm các quân nhân thuộc đơn vị này
        foreach ($unit->soldiers as $soldier) {
            $soldier->delete();
        }
    }

    /**
     * Handle the Unit "restoring" event.
     */
    public function restoring(Unit $unit): void
    {
        // Khôi phục các đơn vị con đã bị xóa mềm cùng lúc với đơn vị này
        // (Kiểm tra deleted_at gần với deleted_at của đơn vị cha)
        $children = Unit::onlyTrashed()
            ->where('parent_id', $unit->id)
            ->where('deleted_at', '>=', $unit->deleted_at)
            ->get();

        foreach ($children as $child) {
            $child->restore();
        }

        // Khôi phục các quân nhân thuộc đơn vị này đã bị xóa mềm cùng lúc
        $soldiers = \App\Models\Soldier::onlyTrashed()
            ->where('unit_id', $unit->id)
            ->where('deleted_at', '>=', $unit->deleted_at)
            ->get();

        foreach ($soldiers as $soldier) {
            $soldier->restore();
        }
    }

    protected function clearUserUnitCache()
    {
        // Thay vì xóa toàn bộ cache hệ thống bằng Artisan::call('cache:clear'),
        // chúng ta sẽ xóa có mục tiêu các cache key liên quan đến phân cấp đơn vị của từng user.
        // Điều này giúp giữ lại các cache khác (session, config, views, v.v...) và cải thiện hiệu suất.

        User::pluck('id')->each(function ($userId) {
            Cache::forget("user_{$userId}_accessible_unit_ids");
            Cache::forget("user_{$userId}_navigable_unit_ids");
        });

        // Xóa thêm các cache chung nếu có (ví dụ: danh sách đơn vị cho dropdown toàn hệ thống)
        Cache::forget('all_units_tree');
    }
}
