<?php

namespace App\Services;

use App\Models\Trainings;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrainingService
{
    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Trainings::with(['team', 'creator']);

        if ($user->isRole('coach') || $user->isRole('manager')) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        if (! empty($filters['team_id'])) {
            $query->where('team_id', $filters['team_id']);
        }

        if (! empty($filters['training_type'])) {
            $query->where('training_type', $filters['training_type']);
        }

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['date_from'])) {
            $query->where('training_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('training_date', '<=', $filters['date_to']);
        }

        $perPage = max(1, min((int) ($filters['per_page'] ?? 15), 100));

        return $query->latest('training_date')->paginate($perPage);
    }

    public function show(int $id, User $user): Trainings
    {
        $training = Trainings::with(['team', 'creator', 'performances.player.position'])->findOrFail($id);

        $this->authorizeTeamAccess($user, $training);

        return $training;
    }

    public function create(array $data, User $user): Trainings
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $data['team_id']);
        }

        $data['created_by'] = $user->id;

        $training = Trainings::create($data);

        return $training->load(['team', 'creator']);
    }

    public function update(int $id, array $data, User $user): Trainings
    {
        $training = Trainings::findOrFail($id);

        $this->authorizeTeamAccess($user, $training);

        if (isset($data['team_id']) && $data['team_id'] !== $training->team_id) {
            if ($user->isRole('coach') || $user->isRole('manager')) {
                $this->authorizeForTeam($user, $data['team_id']);
            }
        }

        $training->update($data);

        return $training->fresh(['team', 'creator']);
    }

    public function delete(int $id, User $user): void
    {
        $training = Trainings::findOrFail($id);

        $this->authorizeTeamAccess($user, $training);

        $training->delete();
    }

    private function authorizeTeamAccess(User $user, Trainings $training): void
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            $this->authorizeForTeam($user, $training->team_id);
        }
    }

    private function authorizeForTeam(User $user, int $teamId): void
    {
        if (! $user->getTeamIds()->contains($teamId)) {
            abort(403, 'Bu takıma erişim yetkiniz yok.');
        }
    }
}
