<?php

namespace App\Services;

use App\Models\Matches;
use App\Models\PlayerMatchStats;
use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MatchStatsService
{
    public function __construct(
        protected MatchService $matchService,
        private TeamAccess $teamAccess
    ) {}

    public function listByMatch(int $matchId, User $user): Collection
    {
        $match = $this->matchService->show($matchId, $user);

        return $match->playerMatchStats->load('player.position');
    }

    public function upsert(int $matchId, array $data, User $user): PlayerMatchStats
    {
        $match = $this->matchService->show($matchId, $user);
        $player = Players::findOrFail($data['player_id']);
        $this->teamAccess->assertMatchPlayer($match, $player);

        return PlayerMatchStats::updateOrCreate(
            [
                'match_id' => $matchId,
                'player_id' => $data['player_id'],
            ],
            collect($data)->except('player_id')->toArray()
        );
    }

    public function bulkUpsert(int $matchId, array $players, User $user): Collection
    {
        $match = $this->matchService->show($matchId, $user);

        $results = new Collection;

        foreach ($players as $playerData) {
            $player = Players::findOrFail($playerData['player_id']);
            $this->teamAccess->assertMatchPlayer($match, $player);

            $stat = PlayerMatchStats::updateOrCreate(
                [
                    'match_id' => $matchId,
                    'player_id' => $playerData['player_id'],
                ],
                collect($playerData)->except('player_id')->toArray()
            );

            $results->push($stat);
        }

        return $results->load('player.position');
    }

    public function historyForPlayer(User $user, array $filters = []): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return [
                'summary' => $this->emptySummary(),
                'stats' => PlayerMatchStats::query()
                    ->whereRaw('1 = 0')
                    ->paginate($this->perPage($filters)),
            ];
        }

        $query = $this->playerHistoryQuery($player->id, $filters);

        return [
            'summary' => $this->buildSummary($query),
            'stats' => $this->paginateHistory($query, $filters),
        ];
    }

    public function recentHistoryForPlayer(User $user, int $limit = 5): Collection
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return collect();
        }

        return $this->orderByMatchDate($this->playerHistoryQuery($player->id, []))
            ->with(['match.team'])
            ->limit($limit)
            ->get();
    }

    public function summaryForPlayer(User $user, array $filters = []): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return $this->emptySummary();
        }

        return $this->buildSummary($this->playerHistoryQuery($player->id, $filters));
    }

    private function playerHistoryQuery(int $playerId, array $filters): Builder
    {
        $query = PlayerMatchStats::query()
            ->where('player_id', $playerId)
            ->whereHas('match');

        if (! empty($filters['match_type'])) {
            $query->whereHas('match', fn (Builder $q) => $q->where('match_type', $filters['match_type']));
        }

        if (! empty($filters['date_from'])) {
            $query->whereHas('match', fn (Builder $q) => $q->where('match_date', '>=', $filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereHas('match', fn (Builder $q) => $q->where('match_date', '<=', $filters['date_to']));
        }

        return $query;
    }

    private function paginateHistory(Builder $query, array $filters): LengthAwarePaginator
    {
        return $this->orderByMatchDate($query)
            ->with(['match.team', 'player.position'])
            ->paginate($this->perPage($filters));
    }

    private function orderByMatchDate(Builder $query): Builder
    {
        return $query->orderByDesc(
            Matches::select('match_date')
                ->whereColumn('matches.id', 'player_match_stats.match_id')
                ->limit(1)
        );
    }

    private function buildSummary(Builder $query): array
    {
        $total = (clone $query)->count();
        $starts = (clone $query)->where('is_starting', true)->count();
        $minutes = (clone $query)->sum('minutes_played');
        $goals = (clone $query)->sum('goals');
        $assists = (clone $query)->sum('assists');
        $yellowCards = (clone $query)->sum('yellow_cards');
        $redCards = (clone $query)->sum('red_cards');
        $averageRating = (clone $query)->whereNotNull('match_rating')->avg('match_rating');
        $averagePassAccuracy = (clone $query)->whereNotNull('pass_accuracy')->avg('pass_accuracy');

        return [
            'total_matches' => $total,
            'starts' => $starts,
            'minutes' => (int) $minutes,
            'goals' => (int) $goals,
            'assists' => (int) $assists,
            'average_rating' => $averageRating !== null ? round((float) $averageRating, 2) : null,
            'average_pass_accuracy' => $averagePassAccuracy !== null ? round((float) $averagePassAccuracy, 2) : null,
            'yellow_cards' => (int) $yellowCards,
            'red_cards' => (int) $redCards,
        ];
    }

    private function emptySummary(): array
    {
        return [
            'total_matches' => 0,
            'starts' => 0,
            'minutes' => 0,
            'goals' => 0,
            'assists' => 0,
            'average_rating' => null,
            'average_pass_accuracy' => null,
            'yellow_cards' => 0,
            'red_cards' => 0,
        ];
    }

    private function perPage(array $filters): int
    {
        return max(1, min((int) ($filters['per_page'] ?? 15), 100));
    }
}
