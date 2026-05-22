<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leagues extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'season',
        'description',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Teams::class, 'league_team', 'league_id', 'team_id')->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class, 'league_id');
    }
}
