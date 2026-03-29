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

        if ($user->isRole('coach')) {
            $teamIds = $user->teams()->pluck('teams.id');
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

        $perPage = min($filters['per_page'] ?? 15, 100);

        return $query->latest()->paginate($perPage);
    }

    public function show(int $id, User $user): Teams
    {
        $team = Teams::with(['players.position', 'coaches'])->findOrFail($id);

        $this->authorizeCoach($user, $team);

        return $team;
    }

    public function create(array $data): Teams
    {
        return Teams::create($data);
    }

    public function update(int $id, array $data, User $user): Teams
    {
        $team = Teams::findOrFail($id);

        $this->authorizeCoach($user, $team);

        $team->update($data);

        return $team->fresh();
    }

    public function delete(int $id, User $user): void
    {
        $team = Teams::findOrFail($id);

        $this->authorizeCoach($user, $team);

        $team->delete();
    }

    public function assignCoach(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);

        $team->coaches()->syncWithoutDetaching([$userId]);
    }

    public function removeCoach(int $teamId, int $userId): void
    {
        $team = Teams::findOrFail($teamId);

        $team->coaches()->detach($userId);
    }

    private function authorizeCoach(User $user, Teams $team): void
    {
        if ($user->isRole('coach') && ! $user->teams()->where('teams.id', $team->id)->exists()) {
            abort(403, 'You are not assigned to this team.');
        }
    }
}
