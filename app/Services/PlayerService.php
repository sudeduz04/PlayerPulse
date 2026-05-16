<?php

namespace App\Services;

use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlayerService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Players::with(['team', 'position']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $query->where('user_id', $user->id);
        }

        if (! empty($filters['team_id'])) {
            $query->where('team_id', $filters['team_id']);
        }

        if (! empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            });
        }

        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

        return $query->latest()->paginate($perPage);
    }

    public function show(int $id, User $user): Players
    {
        $player = Players::with([
            'team', 'position', 'user',
            'injuries', 'physicalMeasurements', 'notes.author', 'developmentReports.creator',
        ])->findOrFail($id);

        $this->teamAccess->assertPlayer($user, $player);

        return $player;
    }

    public function create(array $data, User $user): Players
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular oyuncu kaydı yönetemez.');

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $this->teamAccess->assertTeam($user, (int) $data['team_id']);
        }

        $player = Players::create($data);

        return $player->load(['team', 'position']);
    }

    public function update(int $id, array $data, User $user): Players
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular oyuncu kaydı yönetemez.');

        $player = Players::findOrFail($id);

        $this->teamAccess->assertPlayer($user, $player);

        if (isset($data['team_id']) && (int) $data['team_id'] !== $player->team_id) {
            if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
                $this->teamAccess->assertTeam($user, (int) $data['team_id']);
            }
        }

        $player->update($data);

        return $player->fresh(['team', 'position']);
    }

    public function delete(int $id, User $user): void
    {
        abort_if($user->isRole(User::ROLE_PLAYER), 403, 'Oyuncular oyuncu kaydı yönetemez.');

        $player = Players::findOrFail($id);

        $this->teamAccess->assertPlayer($user, $player);

        $player->delete();
    }
}
