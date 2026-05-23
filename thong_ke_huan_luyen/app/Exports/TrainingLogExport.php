<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Họ và tên quân nhân',
            'Đơn vị',
            'Nội dung huấn luyện',
            'Quân số (Yêu cầu/Thực tế)',
            'Thời gian (Yêu cầu/Thực tế)',
            'Kết quả kiểm tra',
            'Xếp loại',
            'Nhận xét chung'
        ];
    }

    public function map($log): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $log->training_date ? $log->training_date->format('d/m/Y') : '',
            $log->soldier_name_at_time,
            $log->unit_name_at_time,
            strip_tags($log->training_content),
            ($log->required_quanso ?? 0) . '/' . ($log->actual_quanso ?? 0),
            ($log->required_hours ?? 0) . '/' . ($log->actual_hours ?? 0),
            $log->test_quanso ? "Sát hạch: {$log->test_quanso}" : 'N/A',
            $log->getRatingNameAttribute(),
            $log->general_evaluation
        ];
    }
}
