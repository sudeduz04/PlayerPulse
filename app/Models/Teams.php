<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teams extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'age_category',
        'season',
        'description',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Teams $team) {
            $team->players()->delete();
            $team->staff()->detach();
        });
    }

    public function players(): HasMany
    {
        return $this->hasMany(Players::class, 'team_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id');
    }

    public function coaches(): BelongsToMany
    {
        return $this->staff()->where('role', User::ROLE_COACH);
    }

    public function managers(): BelongsToMany
    {
        return $this->staff()->where('role', User::ROLE_MANAGER);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Trainings::class, 'team_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class, 'team_id');
    }
}
