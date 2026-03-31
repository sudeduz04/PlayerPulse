<?php

namespace App\Services;

use App\Models\Teams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamService
{
    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Teams::query()->withCount(['players', 'coaches']);

        if ($user->isRole('coach') || $user->isRole('manager')) {
            $teamIds = $user->getTeamIds();
            $query->whereIn('id', $teamIds);
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

        $this->authorizeTeamAccess($user, $team);

        return $team;
    }

    public function create(array $data): Teams
    {
        return Teams::create($data);
    }

    public function update(int $id, array $data, User $user): Teams
    {
        $team = Teams::findOrFail($id);

        $this->authorizeTeamAccess($user, $team);

        $team->update($data);

        return $team->fresh();
    }

    public function delete(int $id, User $user): void
    {
        if ($user->isRole('coach')) {
            abort(403, 'Antrenörler takım silemez.');
        }

        $team = Teams::findOrFail($id);

        $this->authorizeTeamAccess($user, $team);

        $team->delete();
    }

    public function assignStaff(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);

        $team->staff()->syncWithoutDetaching([$userId]);
    }

    public function removeStaff(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);

        $team->staff()->detach($userId);
    }

    private function authorizeTeamAccess(User $user, Teams $team): void
    {
        if (($user->isRole('coach') || $user->isRole('manager')) && ! $user->getTeamIds()->contains($team->id)) {
            abort(403, 'Bu takıma erişim yetkiniz yok.');
        }
    }
}
