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

    /**
     * Kullanıcının atandığı takım perspektifinden rakip adını döndürür.
     * Kullanıcının takımı maça dahil değilse veya super_admin ise ev sahibi vs deplasman gösterir.
     */
    public function opponentForUser(?User $user): string
    {
        $myTeamIds = $user?->getTeamIds()->all() ?? [];

        if ($this->home_team_id && in_array($this->home_team_id, $myTeamIds, true)) {
            return $this->awayTeam?->name ?? $this->opponent_team ?? '-';
        }
        if ($this->away_team_id && in_array($this->away_team_id, $myTeamIds, true)) {
            return $this->homeTeam?->name ?? '-';
        }
        if ($this->team_id && in_array($this->team_id, $myTeamIds, true)) {
            return $this->opponent_team ?? $this->awayTeam?->name ?? '-';
        }

        // Kullanıcının takımı bu maçta yok (örn super_admin) → ev sahibi vs deplasman
        $home = $this->homeTeam?->name ?? $this->team?->name ?? '-';
        $away = $this->awayTeam?->name ?? $this->opponent_team ?? '-';
        return $home.' vs '.$away;
    }

    /**
     * Kullanıcı takımının bu maçta ev sahibi mi deplasman mı olduğunu döndürür.
     * 'home' | 'away' | null (takım maçta değilse)
     */
    public function sideForUser(?User $user): ?string
    {
        $myTeamIds = $user?->getTeamIds()->all() ?? [];

        if ($this->home_team_id && in_array($this->home_team_id, $myTeamIds, true)) {
            return 'home';
        }
        if ($this->away_team_id && in_array($this->away_team_id, $myTeamIds, true)) {
            return 'away';
        }
        if ($this->team_id && in_array($this->team_id, $myTeamIds, true)) {
            return 'home';
        }
        return null;
    }

    /**
     * Kullanıcı takımının attığı gol (lehte).
     */
    public function goalsForUser(?User $user): int
    {
        return $this->sideForUser($user) === 'away'
            ? (int) ($this->goals_against ?? 0)
            : (int) ($this->goals_for ?? 0);
    }

    /**
     * Kullanıcı takımına atılan gol (aleyhte).
     */
    public function goalsAgainstUser(?User $user): int
    {
        return $this->sideForUser($user) === 'away'
            ? (int) ($this->goals_for ?? 0)
            : (int) ($this->goals_against ?? 0);
    }

    /**
     * Kullanıcı takımı perspektifinden sonuç: 'Galibiyet' | 'Mağlubiyet' | 'Beraberlik' | null (oynanmadıysa)
     */
    public function resultForUser(?User $user): ?string
    {
        if ($this->status !== self::STATUS_FINISHED) {
            return null;
        }
        $for = $this->goalsForUser($user);
        $against = $this->goalsAgainstUser($user);

        if ($for > $against) return 'Galibiyet';
        if ($for < $against) return 'Mağlubiyet';
        return 'Beraberlik';
    }
}
