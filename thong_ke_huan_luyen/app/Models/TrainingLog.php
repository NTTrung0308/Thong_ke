<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_id', 'unit_name_at_time',
        'soldier_id', 'soldier_name_at_time',
        'training_date', 'day_of_week',
        'attendance_mon', 'attendance_tue', 'attendance_wed',
        'attendance_thu', 'attendance_fri', 'attendance_sat', 'attendance_sun',
        'training_content',
        'required_quanso', 'actual_quanso', 'absent_quanso',
        'required_hours', 'actual_hours',
        'test_quanso',
        'good_count', 'good_percent',
        'fair_count', 'fair_percent',
        'pass_count', 'pass_percent',
        'fail_count', 'fail_percent',
        'rating', 'general_evaluation', 'notes',
        'instructor', 'commander',
        'attachment', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'training_date' => 'date',
        'good_percent' => 'float',
        'fair_percent' => 'float',
        'pass_percent' => 'float',
        'fail_percent' => 'float',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFormattedTrainingDateAttribute()
    {
        return $this->training_date ? $this->training_date->format('d/m/Y') : '';
    }

    public function getAttendanceArrayAttribute()
    {
        return [
            'Thứ 2' => $this->attendance_mon,
            'Thứ 3' => $this->attendance_tue,
            'Thứ 4' => $this->attendance_wed,
            'Thứ 5' => $this->attendance_thu,
            'Thứ 6' => $this->attendance_fri,
            'Thứ 7' => $this->attendance_sat,
            'Chủ nhật' => $this->attendance_sun,
        ];
    }

    public function getRatingNameAttribute()
    {
        $ratings = [
            'xuất_sắc' => 'Xuất sắc',
            'giỏi' => 'Giỏi',
            'khá' => 'Khá',
            'trung_bình' => 'Trung bình',
            'yếu' => 'Yếu'
        ];
        return $ratings[$this->rating] ?? 'Chưa xếp loại';
    }

    public function getRatingBadgeAttribute()
    {
        $badges = [
            'xuất_sắc' => 'badge-success',
            'giỏi' => 'badge-primary',
            'khá' => 'badge-info',
            'trung_bình' => 'badge-warning',
            'yếu' => 'badge-danger'
        ];
        return $badges[$this->rating] ?? 'badge-secondary';
    }

    public function getAttendanceRateAttribute()
    {
        if ($this->required_quanso == 0) return 0;
        return round(($this->actual_quanso / $this->required_quanso) * 100, 2);
    }

    public function getTimeRateAttribute()
    {
        if ($this->required_hours == 0) return 0;
        return round(($this->actual_hours / $this->required_hours) * 100, 2);
    }

    // Scopes
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('training_date', [$startDate, $endDate]);
    }
}
