<?php

namespace App\Observers;

use App\Models\Unit;
use Illuminate\Support\Facades\Artisan;
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

    protected function clearUserUnitCache()
    {
        // Xóa tất cả cache liên quan đến quyền truy cập đơn vị của user
        // Trong môi trường production, có thể dùng cache tags nếu driver hỗ trợ (Redis/Memcached)
        // Ở đây ta dùng cách đơn giản là xóa theo pattern hoặc xóa toàn bộ cache nếu cần
        // Tuy nhiên Laravel File cache không hỗ trợ xóa theo pattern dễ dàng.
        // Vì số lượng user có thể không quá lớn, ta có thể chấp nhận việc cache tự hết hạn sau 1 giờ
        // Hoặc dùng Artisan command để xóa cache.
        Artisan::call('cache:clear');
    }
}
