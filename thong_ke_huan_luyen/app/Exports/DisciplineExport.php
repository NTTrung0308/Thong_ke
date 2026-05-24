<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DisciplineExport implements FromCollection, WithHeadings, WithMapping
{
    protected $disciplines;

    public function __construct($disciplines)
    {
        $this->disciplines = $disciplines;
    }

    public function collection()
    {
        return $this->disciplines;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên quân nhân',
            'Đơn vị',
            'Nội dung sai phạm',
            'Hình thức kỷ luật',
            'Ngày quyết định',
            'Số quyết định',
            'Cấp quyết định',
            'Tình trạng',
            'Ghi chú',
        ];
    }

    public function map($discipline): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $discipline->soldier_name_at_time,
            $discipline->unit_name_at_time,
            $discipline->violation_details,
            $discipline->discipline_form,
            $discipline->decision_date ? $discipline->decision_date->format('d/m/Y') : '',
            $discipline->decision_number,
            $discipline->decision_level,
            $discipline->getStatusNameAttribute(),
            $discipline->result,
        ];
    }
}
