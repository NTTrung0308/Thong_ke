<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Discipline extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'unit_id', 'unit_name_at_time',
        'soldier_id', 'soldier_name_at_time', 'soldier_rank_at_time',
        'work_content', 'violation_details',
        'discipline_form', 'decision_date', 'decision_month',
        'decision_level', 'decision_number',
        'signer_name', 'signer_position',
        'execution_date', 'expiry_date',
        'result', 'improvement_measures',
        'attachment', 'status',
        'created_by', 'updated_by'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'decision_date' => 'date',
        'execution_date' => 'date',
        'expiry_date' => 'date',
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
    public function getFormattedDecisionDateAttribute()
    {
        return $this->decision_date ? $this->decision_date->format('d/m/Y') : '';
    }

    public function getFormattedExecutionDateAttribute()
    {
        return $this->execution_date ? $this->execution_date->format('d/m/Y') : '';
    }

    public function getFormattedExpiryDateAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : '';
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'dang-thi-hanh' => 'Đang thi hành',
            'da-thi-hanh-xong' => 'Đã thi hành xong',
            'duoc-xoa-bo' => 'Được xóa bỏ'
        ];
        return $statuses[$this->status] ?? 'Không xác định';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'dang-thi-hanh' => 'badge-warning',
            'da-thi-hanh-xong' => 'badge-success',
            'duoc-xoa-bo' => 'badge-secondary'
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }

    // Scopes
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeBySoldier($query, $soldierId)
    {
        return $query->where('soldier_id', $soldierId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('decision_date', $year);
    }
}
