<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matches extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'opponent_team',
        'match_date',
        'match_type',
        'location',
        'result',
        'goals_for',
        'goals_against',
        'coach_note',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function playerMatchStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'match_id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineups::class, 'match_id');
    }
}
