<?php

namespace App\Services;

use App\Models\Teams;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Teams::query()->withCount(['players', 'coaches']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('id', $user->getTeamIds());
        }

        if (! empty($filters['age_category'])) {
            $query->where('age_category', $filters['age_category']);
        }

        if (! empty($filters['season'])) {
            $query->where('season', $filters['season']);
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

        return $query->latest()->paginate($perPage);
    }

    public function show(int $id, User $user): Teams
    {
        $team = Teams::with(['players.position', 'staff'])->findOrFail($id);

        $this->teamAccess->assertTeam($user, $team);

        return $team;
    }

    public function create(array $data): Teams
    {
        return Teams::create($data);
    }

    public function update(int $id, array $data, User $user): Teams
    {
        $team = Teams::findOrFail($id);

        $this->teamAccess->assertTeam($user, $team);

        $team->update($data);

        return $team->fresh();
    }

    public function delete(int $id, User $user): void
    {
        abort_unless($user->isSuperAdmin(), 403, 'Sadece süper yöneticiler takım silebilir.');

        $team = Teams::findOrFail($id);

        $this->teamAccess->assertTeam($user, $team);

        $team->delete();
    }

    public function assignStaff(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);
        $user = User::findOrFail($userId);

        abort_unless(
            $user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER),
            422,
            'Takıma yalnızca antrenör veya yönetici atanabilir.'
        );

        $team->staff()->syncWithoutDetaching([$userId]);
    }

    public function removeStaff(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);

        $team->staff()->detach($userId);
    }
}
