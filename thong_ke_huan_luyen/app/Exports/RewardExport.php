<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RewardExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rewards;

    public function __construct($rewards)
    {
        $this->rewards = $rewards;
    }

    public function collection()
    {
        return $this->rewards;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên quân nhân',
            'Đơn vị',
            'Lý do khen thưởng',
            'Hình thức',
            'Ngày quyết định',
            'Số quyết định',
            'Cấp quyết định',
            'Người ký',
            'Ghi chú',
        ];
    }

    public function map($reward): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $reward->soldier_name_at_time,
            $reward->unit_name_at_time,
            $reward->reason,
            $reward->reward_form,
            $reward->decision_date ? $reward->decision_date->format('d/m/Y') : '',
            $reward->decision_number,
            $reward->decision_level,
            $reward->signer_name,
            $reward->result,
        ];
    }
}
