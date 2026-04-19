<?php

namespace App\Services;

use App\Models\Players;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlayerService
{
    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Players::with(['team', 'position']);

        if ($user->isRole('coach') || $user->isRole('manager')) {
            $teamIds = $user->getTeamIds();
            $query->whereIn('team_id', $teamIds);
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

        $this->authorizeTeamAccess($user, $player);

        return $player;
    }

    public function create(array $data, User $user): Players
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $data['team_id']);
        }

        $player = Players::create($data);

        return $player->load(['team', 'position']);
    }

    public function update(int $id, array $data, User $user): Players
    {
        $player = Players::findOrFail($id);

        $this->authorizeTeamAccess($user, $player);

        if (isset($data['team_id']) && $data['team_id'] !== $player->team_id) {
            if ($user->isRole('coach') || $user->isRole('manager')) {
                $this->authorizeForTeam($user, $data['team_id']);
            }
        }

        $player->update($data);

        return $player->fresh(['team', 'position']);
    }

    public function delete(int $id, User $user): void
    {
        $player = Players::findOrFail($id);

        $this->authorizeTeamAccess($user, $player);

        $player->delete();
    }

    private function authorizeTeamAccess(User $user, Players $player): void
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $player->team_id);
        }
    }

    private function authorizeForTeam(User $user, int $teamId): void
    {
        if (! $user->getTeamIds()->contains($teamId)) {
            abort(403, 'Bu takıma erişim yetkiniz yok.');
        }
    }
}
