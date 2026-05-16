<?php

namespace App\Services;

use App\Models\Trainings;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrainingService
{
    public function __construct(private TeamAccess $teamAccess) {}

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = Trainings::with(['team', 'creator']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            $query->where('team_id', $user->player?->team_id);
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

        $this->teamAccess->assertTeam($user, $training->team_id);

        return $training;
    }

    public function create(array $data, User $user): Trainings
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        if ($user->isRole(User::ROLE_COACH)) {
            $this->teamAccess->assertTeam($user, (int) $data['team_id']);
        }

        $data['created_by'] = $user->id;

        $training = Trainings::create($data);

        return $training->load(['team', 'creator']);
    }

    public function update(int $id, array $data, User $user): Trainings
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $training = Trainings::findOrFail($id);

        $this->teamAccess->assertTeam($user, $training->team_id);

        if (isset($data['team_id']) && (int) $data['team_id'] !== $training->team_id) {
            if ($user->isRole(User::ROLE_COACH)) {
                $this->teamAccess->assertTeam($user, (int) $data['team_id']);
            }
        }

        $training->update($data);

        return $training->fresh(['team', 'creator']);
    }

    public function delete(int $id, User $user): void
    {
        abort_unless(
            $user->isSuperAdmin() || $user->isRole(User::ROLE_COACH),
            403,
            'Bu işlem için antrenör veya süper yönetici yetkisi gerekir.'
        );

        $training = Trainings::findOrFail($id);

        $this->teamAccess->assertTeam($user, $training->team_id);

        $training->delete();
    }
}
