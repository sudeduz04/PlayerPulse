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
        $query = Matches::with(['team']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $query->where('team_id', $user->player?->team_id);
        }

        if (! empty($filters['team_id'])) {
            $query->where('team_id', $filters['team_id']);
        }

        if (! empty($filters['match_type'])) {
            $query->where('match_type', $filters['match_type']);
        }

        if (! empty($filters['search'])) {
            $query->where('opponent_team', 'like', '%'.$filters['search'].'%');
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
        $match = Matches::with(['team', 'playerMatchStats'])->findOrFail($id);

        $this->teamAccess->assertTeam($user, $match->team_id);

        return $match;
    }

    public function create(array $data, User $user): Matches
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        if ($user->isRole(User::ROLE_COACH)) {
            $this->teamAccess->assertTeam($user, (int) $data['team_id']);
        }

        $match = Matches::create($data);

        return $match->load(['team']);
    }

    public function update(int $id, array $data, User $user): Matches
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $match = Matches::findOrFail($id);

        $this->teamAccess->assertTeam($user, $match->team_id);

        if (isset($data['team_id']) && (int) $data['team_id'] !== $match->team_id) {
            if ($user->isRole(User::ROLE_COACH)) {
                $this->teamAccess->assertTeam($user, (int) $data['team_id']);
            }
        }

        $match->update($data);

        return $match->fresh(['team']);
    }

    public function delete(int $id, User $user): void
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $match = Matches::findOrFail($id);

        $this->teamAccess->assertTeam($user, $match->team_id);

        $match->delete();
    }
}
