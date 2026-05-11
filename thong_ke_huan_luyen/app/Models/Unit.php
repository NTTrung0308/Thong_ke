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
            'chi-huy' => 'Bộ chỉ huy',
            'trung-doan' => 'Trung đoàn',
            'tieu-doan' => 'Tiểu đoàn',
            'dai-doi' => 'Đại đội',
            'trung-doi' => 'Trung đội'
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

    public function getAllDescendantIds($visited = [])
    {
        if (in_array($this->id, $visited)) {
            return [];
        }
        $visited[] = $this->id;
        
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds($visited));
        }
        return $ids;
    }

    public function getAncestors()
    {
        $ancestors = collect([]);
        $parent = $this->parent;
        $visited = [$this->id];

        while ($parent && !in_array($parent->id, $visited)) {
            $ancestors->push($parent);
            $visited[] = $parent->id;
            $parent = $parent->parent;
        }
        return $ancestors->reverse();
    }

    public function getFullHierarchyName()
    {
        $ancestors = $this->getAncestors();
        if ($ancestors->isEmpty()) {
            return $this->name;
        }
        // Trả về theo thứ tự: Trung đội - Đại đội - Tiểu đoàn - Trung đoàn - Bộ chỉ huy khu vực
        // collect([$this]) là đơn vị hiện tại (thường là cấp thấp nhất của soldier)
        // ancestors->reverse() là các cấp trên theo thứ tự từ thấp đến cao
        return collect([$this])->concat($this->getAncestors()->reverse())->pluck('name')->implode(' - ');
    }
}
