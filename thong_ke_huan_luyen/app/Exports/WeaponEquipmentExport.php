<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WeaponEquipmentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $equipments;

    public function __construct($equipments)
    {
        $this->equipments = $equipments;
    }

    public function collection()
    {
        return $this->equipments;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên',
            'Đơn vị',
            'AK',
            'RPD',
            'B41',
            'M79',
            'Thông nòng',
            'Phụ tùng',
            'Dây súng',
            'Hộp tiếp đạn',
            'Vịt dầu',
            'Bao đồ',
            'Áo súng',
            'Bịt nòng',
            'Kính ngắm',
            'Lựu đạn',
            'Xẻng BB',
            'Cuốc BB',
            'Tình trạng',
            'Trạng thái',
            'Ngày nhận',
            'Ghi chú',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        $statusLabels = [
            'dang-su-dung' => 'Đang sử dụng',
            'da-tra' => 'Đã trả',
            'dang-bao-duong' => 'Đang bảo dưỡng',
        ];

        $conditionLabels = [
            'tot' => 'Tốt',
            'hỏng' => 'Hỏng',
            'cần_bảo_dưỡng' => 'Cần bảo dưỡng',
        ];

        return [
            $index,
            $item->soldier->full_name ?? 'N/A',
            $item->unit->name ?? 'N/A',
            $item->ak,
            $item->rpd,
            $item->b41,
            $item->m79,
            $item->cleaning_rod,
            $item->spare_parts,
            $item->gun_strap,
            $item->magazine_box,
            $item->oil_can,
            $item->bag,
            $item->gun_cover,
            $item->muzzle_cover,
            $item->sight,
            $item->grenade,
            $item->infantry_shovel,
            $item->infantry_pickaxe,
            $conditionLabels[$item->condition] ?? $item->condition,
            $statusLabels[$item->status] ?? $item->status,
            $item->receive_date ? $item->receive_date->format('d/m/Y') : '',
            $item->notes,
        ];
    }
}
