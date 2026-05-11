<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getAccessibleUnitIds()
    {
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
    }

    public function getNavigableUnitIds()
    {
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
    }

    public function getAccessibleUnits()
    {
        return Unit::whereIn('id', $this->getAccessibleUnitIds())->get();
    }

    public function getRootAccessibleUnits()
    {
        // Luôn bắt đầu từ các đơn vị cấp cao nhất (parent_id is null)
        // Nhưng chỉ lấy những đơn vị mà user có quyền truy cập (trực tiếp hoặc gián tiếp)
        $navigableIds = $this->getNavigableUnitIds();
        
        return Unit::whereNull('parent_id')
            ->whereIn('id', $navigableIds)
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
