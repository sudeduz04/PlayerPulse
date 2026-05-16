<?php

namespace App\Policies;

use App\Models\Trainings;
use App\Models\User;
use App\Services\Authorization\TeamAccess;

class TrainingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER, User::ROLE_COACH], true);
    }

    public function view(User $user, Trainings $training): bool
    {
        return app(TeamAccess::class)->canAccessTeam($user, $training->team_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_COACH], true);
    }

    public function update(User $user, Trainings $training): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_COACH], true)
            && app(TeamAccess::class)->canAccessTeam($user, $training->team_id);
    }

    public function delete(User $user, Trainings $training): bool
    {
        return $this->update($user, $training);
    }
}
