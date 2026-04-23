<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'unit_id', 'unit_name_at_time',
        'soldier_id', 'soldier_name_at_time',
        'reason', 'reward_form', 'decision_date',
        'decision_month', 'decision_level', 'decision_number',
        'signer_name', 'signer_position', 'result',
        'attachment', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'decision_date' => 'date',
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

    // Accessor: Lấy tên loại khen thưởng
    public function getTypeNameAttribute()
    {
        return $this->type === 'unit' ? 'Khen thưởng đơn vị' : 'Khen thưởng cấp trên';
    }

    // Accessor: Format ngày quyết định
    public function getFormattedDecisionDateAttribute()
    {
        return $this->decision_date ? $this->decision_date->format('d/m/Y') : '';
    }

    // Scope: Lọc theo đơn vị
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    // Scope: Lọc theo loại
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope: Lọc theo năm
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('decision_date', $year);
    }
}
