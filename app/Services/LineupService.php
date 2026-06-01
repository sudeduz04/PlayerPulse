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
    public function __construct(
        private TeamAccess $teamAccess,
        private LineupFormationService $formationService,
    ) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Lineups::with(['match.team', 'match.homeTeam', 'match.awayTeam', 'creator']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $teamIds = $user->getTeamIds();
            $query->whereHas('match', function ($q) use ($teamIds) {
                $q->whereIn('team_id', $teamIds)
                    ->orWhereIn('home_team_id', $teamIds)
                    ->orWhereIn('away_team_id', $teamIds);
            });
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
        $lineup = Lineups::with(['match.team', 'match.homeTeam', 'match.awayTeam', 'creator', 'players.player', 'players.position'])->findOrFail($id);

        $this->teamAccess->assertMatch($user, $lineup->match);

        return $lineup;
    }

    public function availableMatches(User $user): Collection
    {
        $query = Matches::with(['team', 'homeTeam', 'awayTeam']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $teamIds = $user->getTeamIds();
            $query->where(function ($q) use ($teamIds) {
                $q->whereIn('team_id', $teamIds)
                    ->orWhereIn('home_team_id', $teamIds)
                    ->orWhereIn('away_team_id', $teamIds);
            });
        }

        return $query->latest('match_date')->get();
    }

    public function rosterForMatch(int $matchId, User $user): Collection
    {
        $match = Matches::findOrFail($matchId);

        $this->teamAccess->assertMatch($user, $match);

        $teamId = $this->resolveUserTeamId($match, $user);

        return Players::with('position')
            ->where('team_id', $teamId)
            ->whereNull('deleted_at')
            ->orderBy('jersey_number')
            ->get();
    }

    /**
     * Maçta hangi takım kullanıcının atandığı takımsa onu döndürür.
     * Kullanıcının takımı maçta yer almıyorsa (ör. super_admin) ev sahibi takım fallback.
     */
    public function resolveUserTeamId(Matches $match, ?User $user): int
    {
        $userTeamIds = $user?->getTeamIds()->all() ?? [];

        foreach (['home_team_id', 'away_team_id', 'team_id'] as $field) {
            $teamId = $match->{$field};
            if ($teamId && in_array($teamId, $userTeamIds, true)) {
                return (int) $teamId;
            }
        }

        return (int) ($match->home_team_id ?: $match->team_id);
    }

    public function positions(): Collection
    {
        return Positions::orderBy('name')->get();
    }

    public function formationSlots(string $formation): array
    {
        return $this->formationService->slots($formation);
    }

    public function create(array $data, User $user, bool $isAiGenerated = false): Lineups
    {
        $match = Matches::findOrFail($data['match_id']);

        $this->teamAccess->assertMatch($user, $match);

        return DB::transaction(function () use ($data, $user, $match, $isAiGenerated) {
            $lineup = Lineups::create([
                'match_id' => $match->id,
                'created_by' => $user->id,
                'formation' => $data['formation'],
                'note' => $data['note'] ?? null,
                'is_ai_generated' => $isAiGenerated,
                'status' => 'completed',
            ]);

            $this->replacePlayers($lineup, $data['players']);

            return $lineup->load(['match.team', 'match.homeTeam', 'match.awayTeam', 'creator', 'players.player', 'players.position']);
        });
    }

    public function createQueued(array $data, User $user): Lineups
    {
        $match = Matches::findOrFail($data['match_id']);
        $this->teamAccess->assertMatch($user, $match);

        return Lineups::create([
            'match_id' => $match->id,
            'created_by' => $user->id,
            'formation' => $data['formation'],
            'note' => $data['note'] ?? null,
            'is_ai_generated' => true,
            'status' => 'queued',
        ]);
    }

    public function completeQueued(Lineups $lineup, array $players, ?string $note = null): Lineups
    {
        return DB::transaction(function () use ($lineup, $players, $note) {
            $lineup->update([
                'note' => $note ?: $lineup->note,
                'status' => 'completed',
                'error_message' => null,
            ]);

            $this->replacePlayers($lineup, $players);

            return $lineup->fresh(['match.team', 'match.homeTeam', 'match.awayTeam', 'creator', 'players.player', 'players.position']);
        });
    }

    public function failQueued(Lineups $lineup, string $message): void
    {
        $lineup->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }

    public function delete(int $id, User $user): void
    {
        $lineup = Lineups::with('match')->findOrFail($id);

        $this->teamAccess->assertMatch($user, $lineup->match);

        DB::transaction(function () use ($lineup) {
            LineupPlayers::where('lineup_id', $lineup->id)->delete();
            $lineup->delete();
        });
    }

    private function replacePlayers(Lineups $lineup, array $players): void
    {
        $match = $lineup->match()->firstOrFail();
        $teamId = $this->resolveUserTeamId($match, $lineup->creator);

        LineupPlayers::where('lineup_id', $lineup->id)->delete();

        foreach ($players as $index => $player) {
            $playerModel = Players::findOrFail($player['player_id']);

            abort_unless(
                $playerModel->team_id === $teamId,
                422,
                'Tum oyuncular kadronun ait oldugu takima ait olmali.'
            );

            LineupPlayers::create([
                'lineup_id' => $lineup->id,
                'player_id' => $player['player_id'],
                'position_id' => $player['position_id'],
                'slot_key' => $player['slot_key'] ?? 'S'.($index + 1),
                'field_x' => $player['field_x'] ?? null,
                'field_y' => $player['field_y'] ?? null,
                'is_starting' => $player['is_starting'] ?? true,
                'recommendation_score' => $player['recommendation_score'] ?? null,
            ]);
        }
    }
}
