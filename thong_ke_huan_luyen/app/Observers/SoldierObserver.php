<?php

namespace App\Observers;

use App\Models\Discipline;
use App\Models\Reward;
use App\Models\Soldier;
use App\Models\TrainingLog;
use App\Models\User;
use App\Models\WeaponEquipment;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SoldierObserver
{
    /**
     * Handle the Soldier "created" event.
     */
    public function created(Soldier $soldier): void
    {
        $creatorId = Auth::id() ?? $soldier->created_by;

        // 1. Tự động tạo bản ghi vũ khí trang bị
        WeaponEquipment::create([
            'soldier_id' => $soldier->id,
            'unit_id' => $soldier->unit_id,
            'status' => 'dang-su-dung',
            'condition' => 'tot',
            'receive_date' => now(),
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);

        // 2. Tự động tạo bản ghi khen thưởng trắng
        Reward::create([
            'type' => 'unit',
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);

        // 3. Tự động tạo bản ghi nhật ký huấn luyện mặc định
        TrainingLog::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'training_date' => now(),
            'day_of_week' => $this->getVietnameseDayOfWeek(now()),
            'training_content' => 'Huấn luyện chiến đấu bộ binh (Mặc định)',
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);

        // 4. Tự động tạo bản ghi kỷ luật trắng
        Discipline::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'soldier_rank_at_time' => $soldier->rank,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'status' => 'da-thi-hanh-xong',
            'created_by' => $creatorId,
            'updated_by' => $creatorId,
        ]);

        // 5. Gửi thông báo cho cấp Chỉ huy
        $chiHuyUsers = User::role('chi-huy')->get();
        if ($chiHuyUsers->count() > 0) {
            $notification = new SystemNotification(
                'Quân nhân mới',
                'Đã thêm mới quân nhân: '.$soldier->full_name.' vào đơn vị '.($soldier->unit->name ?? 'N/A'),
                'fa-user-plus',
                route('soldiers.show', $soldier->id),
                'success'
            );
            Notification::send($chiHuyUsers, $notification);
        }
    }

    /**
     * Handle the Soldier "deleting" event to cascade soft deletes.
     */
    public function deleting(Soldier $soldier): void
    {
        // Xóa mềm các bản ghi liên quan
        $soldier->weapons()->delete();
        $soldier->rewards()->delete();
        $soldier->disciplines()->delete();
        $soldier->trainingLogs()->delete();
    }

    /**
     * Handle the Soldier "restoring" event.
     */
    public function restoring(Soldier $soldier): void
    {
        // Khôi phục các bản ghi liên quan bị xóa cùng lúc
        // (Sử dụng thời gian deleted_at để đảm bảo chỉ khôi phục những bản ghi bị xóa do cascade)
        $soldier->weapons()->onlyTrashed()->where('deleted_at', '>=', $soldier->deleted_at)->restore();
        $soldier->rewards()->onlyTrashed()->where('deleted_at', '>=', $soldier->deleted_at)->restore();
        $soldier->disciplines()->onlyTrashed()->where('deleted_at', '>=', $soldier->deleted_at)->restore();
        $soldier->trainingLogs()->onlyTrashed()->where('deleted_at', '>=', $soldier->deleted_at)->restore();
    }

    /**
     * Handle the Soldier "updated" event.
     */
    public function updated(Soldier $soldier): void
    {
        // Nếu thay đổi đơn vị, cập nhật lại đơn vị cho các bản ghi liên quan đang hoạt động
        if ($soldier->wasChanged('unit_id')) {
            // 1. Cập nhật đơn vị cho vũ khí đang sử dụng
            $soldier->weapons()->update(['unit_id' => $soldier->unit_id]);
            
            // Lưu ý: Với khen thưởng, kỷ luật và nhật ký huấn luyện, 
            // chúng ta thường giữ nguyên đơn vị tại thời điểm xảy ra sự kiện (historical data).
            // Tuy nhiên, nếu muốn các bản ghi này đi theo quân nhân sang đơn vị mới để dễ quản lý:
            // $soldier->rewards()->update(['unit_id' => $soldier->unit_id]);
            // $soldier->disciplines()->update(['unit_id' => $soldier->unit_id]);
            // $soldier->trainingLogs()->update(['unit_id' => $soldier->unit_id]);
        }
    }

    private function getVietnameseDayOfWeek($date)
    {
        $days = [
            'Monday' => 'Thứ 2',
            'Tuesday' => 'Thứ 3',
            'Wednesday' => 'Thứ 4',
            'Thursday' => 'Thứ 5',
            'Friday' => 'Thứ 6',
            'Saturday' => 'Thứ 7',
            'Sunday' => 'Chủ nhật',
        ];

        return $days[$date->format('l')];
    }
}
