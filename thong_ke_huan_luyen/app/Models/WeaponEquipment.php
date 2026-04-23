<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeaponEquipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'weapon_equipment';

    protected $fillable = [
        'soldier_id', 'unit_id',
        'ak', 'rpd', 'b41', 'm79', 'gun_accessories',
        'magazine_box', 'oil_can', 'bag', 'gun_cover',
        'muzzle_cover', 'sight', 'grenade',
        'infantry_shovel', 'infantry_pickaxe',
        'receive_date', 'return_date',
        'received_by', 'returned_by',
        'status', 'notes',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'receive_date' => 'date',
        'return_date' => 'date',
    ];

    // Relationships
    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

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

    // Accessor: Lấy danh sách vũ khí đang có
    public function getWeaponsListAttribute()
    {
        $weapons = [];
        if ($this->ak) $weapons[] = "AK: {$this->ak}";
        if ($this->rpd) $weapons[] = "RPD: {$this->rpd}";
        if ($this->b41) $weapons[] = "B41: {$this->b41}";
        if ($this->m79) $weapons[] = "M79: {$this->m79}";
        return implode(', ', $weapons);
    }

    // Scope: Lọc theo trạng thái
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
