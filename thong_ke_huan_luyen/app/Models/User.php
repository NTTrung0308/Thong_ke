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

    public function getAccessibleUnits()
    {
        return Unit::whereIn('id', $this->getAccessibleUnitIds())->get();
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
