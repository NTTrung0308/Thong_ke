<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingResultExport implements FromCollection, WithHeadings, WithMapping
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Đơn vị',
            'Ngày huấn luyện',
            'Nội dung',
            'Thời lượng (giờ)',
            'Quân số tham gia',
            'Kết quả',
            'Tỷ lệ đạt (%)',
            'Đánh giá'
        ];
    }

    public function map($result): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $result->unit_name_at_time,
            $result->training_date ? $result->training_date->format('d/m/Y') : '',
            $result->content,
            $result->duration_hours,
            $result->trung_doi_count + $result->at_count + $result->kdt_count,
            $result->result_name,
            $result->passing_rate,
            $result->evaluation
        ];
    }
}
