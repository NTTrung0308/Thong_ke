<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level', 'parent_id'];

    public function getLevelLabelAttribute()
    {
        $labels = [
            'chi-huy' => 'Cấp Chỉ huy',
            'trung-doan' => 'Cấp Trung đoàn',
            'tieu-doan' => 'Cấp Tiểu đoàn',
            'dai-doi' => 'Cấp Đại đội',
            'trung-doi' => 'Cấp Trung đội'
        ];
        return $labels[$this->level] ?? $this->level;
    }

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function soldiers()
    {
        return $this->hasMany(Soldier::class);
    }

    // Scope lọc theo cấp đơn vị
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getAllDescendantIds()
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }
}
