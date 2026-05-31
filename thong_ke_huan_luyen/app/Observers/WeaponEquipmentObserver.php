<?php

namespace App\Observers;

use App\Models\WeaponEquipment;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class WeaponEquipmentObserver
{
    /**
     * Handle the WeaponEquipment "updated" event.
     */
    public function updated(WeaponEquipment $weaponEquipment): void
    {
        // Gửi thông báo nếu vũ khí bị hỏng
        if ($weaponEquipment->wasChanged('condition') && $weaponEquipment->condition === 'hỏng') {
            $chiHuys = User::role('chi-huy')->get();
            if ($chiHuys->count() > 0) {
                $soldierName = $weaponEquipment->soldier->full_name ?? 'N/A';
                $unitName = $weaponEquipment->unit->name ?? 'N/A';
                $message = "Vũ khí của quân nhân {$soldierName} ({$unitName}) được báo hỏng.";
                
                $notification = new SystemNotification(
                    'Cảnh báo: Vũ khí hỏng',
                    $message,
                    'fa-exclamation-triangle',
                    route('weapon-equipments.index'),
                    'danger'
                );
                
                Notification::send($chiHuys, $notification);
            }
        }

        // Tự động ghi log vào history nếu có thay đổi quan trọng
        $importantFields = ['status', 'condition', 'unit_id', 'soldier_id'];
        $changes = [];
        foreach ($importantFields as $field) {
            if ($weaponEquipment->wasChanged($field)) {
                $oldValue = $weaponEquipment->getOriginal($field);
                $newValue = $weaponEquipment->$field;
                $changes[] = "$field: $oldValue -> $newValue";
            }
        }

        if (!empty($changes)) {
            $log = "[" . now()->format('d/m/Y H:i') . "] " . (Auth::user()->name ?? 'System') . ": " . implode(', ', $changes);
            $history = $weaponEquipment->history ? $weaponEquipment->history . "\n" . $log : $log;
            
            // Sử dụng updateQuietly để tránh lặp vô hạn
            $weaponEquipment->updateQuietly(['history' => $history]);
        }
    }
}
