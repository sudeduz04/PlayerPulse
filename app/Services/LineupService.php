<?php

namespace App\Services;

use App\Models\LineupPlayers;
use App\Models\Lineups;
use App\Models\Matches;
use App\Models\Players;
use App\Models\Positions;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LineupService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Lineups::with(['match.team', 'creator']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereHas('match', fn ($q) => $q->whereIn('team_id', $user->getTeamIds()));
        }

        if (! empty($filters['match_id'])) {
            $query->where('match_id', $filters['match_id']);
        }

        if (! empty($filters['is_ai_generated'])) {
            $query->where('is_ai_generated', filter_var($filters['is_ai_generated'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest('id')->paginate(15);
    }

    public function show(int $id, User $user): Lineups
    {
        $lineup = Lineups::with(['match.team', 'creator', 'players.player', 'players.position'])->findOrFail($id);

        $this->teamAccess->assertTeam($user, $lineup->match->team_id);

        return $lineup;
    }

    public function availableMatches(User $user): Collection
    {
        $query = Matches::with('team');

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        return $query->latest('match_date')->get();
    }

    public function rosterForMatch(int $matchId, User $user): Collection
    {
        $match = Matches::findOrFail($matchId);

        $this->teamAccess->assertTeam($user, $match->team_id);

        return Players::with('position')
            ->where('team_id', $match->team_id)
            ->whereNull('deleted_at')
            ->orderBy('jersey_number')
            ->get();
    }

    public function positions(): Collection
    {
        return Positions::orderBy('name')->get();
    }

    public function create(array $data, User $user, bool $isAiGenerated = false): Lineups
    {
        $match = Matches::findOrFail($data['match_id']);

        $this->teamAccess->assertTeam($user, $match->team_id);

        return DB::transaction(function () use ($data, $user, $match, $isAiGenerated) {
            $lineup = Lineups::create([
                'match_id' => $match->id,
                'created_by' => $user->id,
                'formation' => $data['formation'],
                'note' => $data['note'] ?? null,
                'is_ai_generated' => $isAiGenerated,
            ]);

            foreach ($data['players'] as $player) {
                $playerModel = Players::findOrFail($player['player_id']);

                abort_unless(
                    $playerModel->team_id === $match->team_id,
                    422,
                    'Tüm oyuncular maçın takımına ait olmalı.'
                );

                LineupPlayers::create([
                    'lineup_id' => $lineup->id,
                    'player_id' => $player['player_id'],
                    'position_id' => $player['position_id'],
                    'is_starting' => $player['is_starting'] ?? true,
                    'recommendation_score' => $player['recommendation_score'] ?? null,
                ]);
            }

            return $lineup->load(['match.team', 'creator', 'players.player', 'players.position']);
        });
    }

    public function delete(int $id, User $user): void
    {
        $lineup = Lineups::findOrFail($id);

        $this->teamAccess->assertTeam($user, $lineup->match->team_id);

        DB::transaction(function () use ($lineup) {
            LineupPlayers::where('lineup_id', $lineup->id)->delete();
            $lineup->delete();
        });
    }
}
