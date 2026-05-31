<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'unit_level', 'created_by'];

    public function parent()
    {
        return $this->belongsTo(TrainingSubject::class, 'parent_id')->withTrashed();
    }

    public function children()
    {
        return $this->hasMany(TrainingSubject::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    /**
     * Get the full path name of the subject (e.g., "Môn A > Bài 1 > Nội dung X")
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }
}
