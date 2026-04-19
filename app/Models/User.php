<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_SUPER_ADMIN = 'super_admin';

    const ROLE_MANAGER = 'manager';

    const ROLE_COACH = 'coach';

    const ROLE_PLAYER = 'player';

    const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_MANAGER,
        self::ROLE_COACH,
        self::ROLE_PLAYER,
    ];

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
        'status',
        'role',
    ];

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function player(): HasOne
    {
        return $this->hasOne(Players::class, 'user_id');
    }

    public function createdTrainings(): HasMany
    {
        return $this->hasMany(Trainings::class, 'created_by');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Teams::class, 'team_user', 'user_id', 'team_id');
    }

    public function getTeamIds(): Collection
    {
        return once(fn () => $this->teams()->pluck('teams.id'));
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }
}
