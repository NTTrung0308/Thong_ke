<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Soldier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code', 'full_name', 'rank', 'position', 'birth_date',
        'enlistment_date', 'party_join_date', 'education', 'foreign_language',
        'professional_level', 'permanent_residence', 'emergency_contact_name',
        'emergency_contact_address', 'notes', 'unit_id', 'created_by', 'updated_by'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'birth_date' => 'date',
        'enlistment_date' => 'date',
        'party_join_date' => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function weapons()
    {
        return $this->hasMany(WeaponEquipment::class, 'soldier_id');
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class, 'soldier_id');
    }

    public function disciplines()
    {
        return $this->hasMany(Discipline::class, 'soldier_id');
    }

    public function trainingLogs()
    {
        return $this->hasMany(TrainingLog::class, 'soldier_id');
    }
}
