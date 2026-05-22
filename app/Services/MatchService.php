<?php

namespace App\Services;

use App\Models\Matches;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MatchService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Matches::with(['team', 'league', 'homeTeam', 'awayTeam']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $teamIds = $user->getTeamIds();
            $query->where(function ($q) use ($teamIds) {
                $q->whereIn('team_id', $teamIds)
                    ->orWhereIn('home_team_id', $teamIds)
                    ->orWhereIn('away_team_id', $teamIds);
            });
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $teamId = $user->player?->team_id;
            $query->where(function ($q) use ($teamId) {
                $q->where('team_id', $teamId)
                    ->orWhere('home_team_id', $teamId)
                    ->orWhere('away_team_id', $teamId);
            });
        }

        if (! empty($filters['team_id'])) {
            $teamId = (int) $filters['team_id'];
            $query->where(function ($q) use ($teamId) {
                $q->where('team_id', $teamId)
                    ->orWhere('home_team_id', $teamId)
                    ->orWhere('away_team_id', $teamId);
            });
        }

        if (! empty($filters['league_id'])) {
            $query->where('league_id', $filters['league_id']);
        }

        if (! empty($filters['week'])) {
            $query->where('week', $filters['week']);
        }

        if (! empty($filters['match_type'])) {
            $query->where('match_type', $filters['match_type']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('opponent_team', 'like', '%'.$search.'%')
                    ->orWhereHas('homeTeam', fn ($team) => $team->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('awayTeam', fn ($team) => $team->where('name', 'like', '%'.$search.'%'));
            });
        }

        if (! empty($filters['date_from'])) {
            $query->where('match_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('match_date', '<=', $filters['date_to']);
        }

        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

        return $query->latest('match_date')->paginate($perPage);
    }

    public function show(int $id, User $user): Matches
    {
        $match = Matches::with(['team', 'league', 'homeTeam', 'awayTeam', 'playerMatchStats'])->findOrFail($id);

        $this->teamAccess->assertMatch($user, $match);

        return $match;
    }

    public function create(array $data, User $user): Matches
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu islem icin antrenor veya super yonetici yetkisi gerekir.'
        );

        $data = $this->normalizeFixtureData($data);

        if ($user->isRole(User::ROLE_COACH)) {
            $this->teamAccess->assertTeam($user, (int) $data['team_id']);
        }

        $match = Matches::create($data);

        return $match->load(['team', 'league', 'homeTeam', 'awayTeam']);
    }

    public function update(int $id, array $data, User $user): Matches
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu islem icin antrenor veya super yonetici yetkisi gerekir.'
        );

        $match = Matches::findOrFail($id);

        $this->teamAccess->assertMatch($user, $match);

        $data = $this->normalizeFixtureData($data);

        if (isset($data['team_id']) && (int) $data['team_id'] !== $match->team_id && $user->isRole(User::ROLE_COACH)) {
            $this->teamAccess->assertTeam($user, (int) $data['team_id']);
        }

        $match->update($data);

        return $match->fresh(['team', 'league', 'homeTeam', 'awayTeam']);
    }

    public function delete(int $id, User $user): void
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu islem icin antrenor veya super yonetici yetkisi gerekir.'
        );

        $match = Matches::findOrFail($id);

        $this->teamAccess->assertMatch($user, $match);

        $match->delete();
    }

    private function normalizeFixtureData(array $data): array
    {
        if (! empty($data['home_team_id'])) {
            $data['team_id'] = $data['home_team_id'];
        }

        if (! empty($data['away_team_id']) && empty($data['opponent_team'])) {
            $data['opponent_team'] = \App\Models\Teams::find($data['away_team_id'])?->name ?? 'Rakip';
        }

        return $data;
    }
}
