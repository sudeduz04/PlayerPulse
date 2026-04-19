<?php

namespace App\Services;

use App\Models\Matches;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MatchService
{
    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Matches::with(['team']);

        if ($user->isRole('coach') || $user->isRole('manager')) {
            $query->whereIn('team_id', $user->getTeamIds());
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

        $this->authorizeTeamAccess($user, $match);

        return $match;
    }

    public function create(array $data, User $user): Matches
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $data['team_id']);
        }

        $match = Matches::create($data);

        return $match->load(['team']);
    }

    public function update(int $id, array $data, User $user): Matches
    {
        $match = Matches::findOrFail($id);

        $this->authorizeTeamAccess($user, $match);

        if (isset($data['team_id']) && $data['team_id'] !== $match->team_id) {
            if ($user->isRole('coach') || $user->isRole('manager')) {
                $this->authorizeForTeam($user, $data['team_id']);
            }
        }

        $match->update($data);

        return $match->fresh(['team']);
    }

    public function delete(int $id, User $user): void
    {
        $match = Matches::findOrFail($id);

        $this->authorizeTeamAccess($user, $match);

        $match->delete();
    }

    private function authorizeTeamAccess(User $user, Matches $match): void
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $match->team_id);
        }
    }

    private function authorizeForTeam(User $user, int $teamId): void
    {
        if (! $user->getTeamIds()->contains($teamId)) {
            abort(403, 'Bu takıma erişim yetkiniz yok.');
        }
    }
}
