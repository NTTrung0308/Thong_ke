<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Soldier extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'code', 'full_name', 'rank', 'position', 'birth_date',
        'enlistment_date', 'party_join_date', 'education', 'foreign_language',
        'professional_level', 'permanent_residence', 'emergency_contact_name',
        'emergency_contact_address', 'notes', 'unit_id', 'created_by', 'updated_by',
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

    // --- Scopes ---

    public function scopeFilterByUnit($query, $unitId)
    {
        if (! $unitId) {
            return $query;
        }

        $unit = Unit::find($unitId);
        if (! $unit) {
            return $query;
        }

        $descendantIds = $unit->getAllDescendantIds();

        return $query->whereIn('unit_id', $descendantIds);
    }

    public function scopeFilterByLevel($query, $level)
    {
        if (! $level) {
            return $query;
        }

        $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
        $currentIndex = array_search($level, $levelsHierarchy);

        if ($currentIndex === false) {
            return $query;
        }

        $targetLevels = array_slice($levelsHierarchy, $currentIndex);

        return $query->whereHas('unit', function ($q) use ($targetLevels) {
            $q->whereIn('level', $targetLevels);
        });
    }

    public function scopeSearchKeywords($query, $q)
    {
        if (! $q) {
            return $query;
        }

        $searchTerm = mb_strtolower($q);

        return $query->where(function ($sub) use ($q, $searchTerm) {
            $sub->where('full_name', 'like', "%$q%")
                ->orWhere('code', 'like', "%$q%")
                ->orWhere('position', 'like', "%$q%")
                ->orWhere('rank', 'like', "%$q%")
                ->orWhere('education', 'like', "%$q%")
                ->orWhere('professional_level', 'like', "%$q%")
                ->orWhereRaw("DATE_FORMAT(birth_date, '%d/%m/%Y') like ?", ["%$q%"])
                ->orWhere('birth_date', 'like', "%$q%")
                ->orWhere('permanent_residence', 'like', "%$q%")
                ->orWhereHas('weapons', function ($w) use ($q, $searchTerm) {
                    $w->where('status', 'dang-su-dung')
                        ->where(function ($wq) use ($q, $searchTerm) {
                            // 1. Tìm theo số hiệu súng (like)
                            $wq->where('ak', 'like', "%$q%")
                                ->orWhere('rpd', 'like', "%$q%")
                                ->orWhere('b41', 'like', "%$q%")
                                ->orWhere('m79', 'like', "%$q%");

                            // 2. Nếu gõ tên loại súng (ví dụ: "ak"), tìm tất cả người được biên chế loại đó
                            if (in_array($searchTerm, ['ak', 'súng ak', 'tiểu liên ak'])) {
                                $wq->orWhereNotNull('ak')->where('ak', '!=', '');
                            }
                            if (in_array($searchTerm, ['rpd', 'súng rpd', 'trung liên rpd'])) {
                                $wq->orWhereNotNull('rpd')->where('rpd', '!=', '');
                            }
                            if (in_array($searchTerm, ['b41', 'súng b41', 'diệt tăng b41'])) {
                                $wq->orWhereNotNull('b41')->where('b41', '!=', '');
                            }
                            if (in_array($searchTerm, ['m79', 'súng m79', 'phóng lựu m79'])) {
                                $wq->orWhereNotNull('m79')->where('m79', '!=', '');
                            }
                        });
                });
        });
    }
}
