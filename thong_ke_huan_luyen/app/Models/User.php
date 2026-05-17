<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['password', 'remember_token'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getAccessibleUnitIds()
    {
        return Cache::remember("user_{$this->id}_accessible_unit_ids", now()->addHours(1), function() {
            if ($this->hasRole('chi-huy')) {
                return Unit::pluck('id')->toArray();
            }

            if ($this->unit) {
                return $this->unit->getAllDescendantIds();
            }

            // Nếu không có đơn vị assigned, lấy theo Role
            $levels = ['trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
            foreach ($levels as $l) {
                if ($this->hasRole($l)) {
                    $levelsHierarchy = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
                    $userLevelIndex = array_search($l, $levelsHierarchy);
                    $allowedLevels = array_slice($levelsHierarchy, $userLevelIndex);
                    
                    return Unit::whereIn('level', $allowedLevels)->pluck('id')->toArray();
                }
            }

            return [];
        });
    }

    public function getNavigableUnitIds()
    {
        return Cache::remember("user_{$this->id}_navigable_unit_ids", now()->addHours(1), function() {
            if ($this->hasRole('chi-huy')) {
                return Unit::pluck('id')->toArray();
            }

            $accessibleIds = $this->getAccessibleUnitIds();
            $navigableIds = $accessibleIds;
            
            $units = Unit::whereIn('id', $accessibleIds)->get();
            foreach ($units as $unit) {
                $ancestors = $unit->getAncestors();
                foreach ($ancestors as $ancestor) {
                    if (!in_array($ancestor->id, $navigableIds)) {
                        $navigableIds[] = $ancestor->id;
                    }
                }
            }
            return $navigableIds;
        });
    }

    public function getAccessibleUnits()
    {
        return Unit::whereIn('id', $this->getAccessibleUnitIds())->get();
    }

    public function getRootAccessibleUnits()
    {
        // Lấy các đơn vị "gốc" trong phạm vi quyền hạn của user
        // Gốc ở đây là đơn vị mà user có quyền truy cập, nhưng parent của nó thì user không có quyền truy cập
        // Hoặc đơn vị đó không có parent (parent_id is null)
        $navigableIds = $this->getNavigableUnitIds();
        
        return Unit::whereIn('id', $navigableIds)
            ->where(function($query) use ($navigableIds) {
                $query->whereNull('parent_id')
                      ->orWhereNotIn('parent_id', $navigableIds);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
