<?php

namespace App\Exports;

use App\Models\Soldier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SoldierExport implements FromCollection, WithHeadings, WithMapping
{
    protected $soldiers;

    public function __construct($soldiers)
    {
        $this->soldiers = $soldiers;
    }

    public function collection()
    {
        return $this->soldiers;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên',
            'Số hiệu quân nhân',
            'Cấp bậc',
            'Chức vụ',
            'Đơn vị',
            'Ngày sinh',
            'Ngày nhập ngũ',
            'Ngày vào Đảng/Đoàn',
            'Trình độ học vấn',
            'Ngoại ngữ',
            'Chuyên môn',
            'Hộ khẩu thường trú',
            'Ghi chú'
        ];
    }

    public function map($soldier): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $soldier->full_name,
            $soldier->code,
            $soldier->rank,
            $soldier->position,
            $soldier->unit ? $soldier->unit->getFullHierarchyName() : 'N/A',
            $soldier->birth_date ? $soldier->birth_date->format('d/m/Y') : '',
            $soldier->enlistment_date ? $soldier->enlistment_date->format('d/m/Y') : '',
            $soldier->party_join_date ? $soldier->party_join_date->format('d/m/Y') : '',
            $soldier->education,
            $soldier->foreign_language,
            $soldier->professional_level,
            $soldier->permanent_residence,
            $soldier->notes
        ];
    }
}
