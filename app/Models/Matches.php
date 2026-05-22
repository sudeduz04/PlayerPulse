<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matches extends Model
{
    use SoftDeletes;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_FIRST_HALF = 'first_half';

    public const STATUS_HALF_TIME = 'half_time';

    public const STATUS_SECOND_HALF = 'second_half';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_POSTPONED = 'postponed';

    public const STATUSES = [
        self::STATUS_SCHEDULED,
        self::STATUS_FIRST_HALF,
        self::STATUS_HALF_TIME,
        self::STATUS_SECOND_HALF,
        self::STATUS_FINISHED,
        self::STATUS_POSTPONED,
    ];

    protected $fillable = [
        'league_id',
        'week',
        'team_id',
        'home_team_id',
        'away_team_id',
        'opponent_team',
        'match_date',
        'kickoff_time',
        'match_type',
        'fixture_source',
        'location',
        'result',
        'status',
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

    public function league(): BelongsTo
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'away_team_id');
    }

    public function playerMatchStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'match_id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineups::class, 'match_id');
    }

    public function involvesTeam(int $teamId): bool
    {
        return $this->team_id === $teamId
            || $this->home_team_id === $teamId
            || $this->away_team_id === $teamId;
    }
}
