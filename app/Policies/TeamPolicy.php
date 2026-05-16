<?php

namespace App\Policies;

use App\Models\Teams;
use App\Models\User;
use App\Services\Authorization\TeamAccess;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER, User::ROLE_COACH], true);
    }

    public function view(User $user, Teams $team): bool
    {
        return app(TeamAccess::class)->canAccessTeam($user, $team->id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Teams $team): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isRole(User::ROLE_MANAGER) && app(TeamAccess::class)->canAccessTeam($user, $team->id);
    }

    public function delete(User $user, Teams $team): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageStaff(User $user, Teams $team): bool
    {
        return $user->isSuperAdmin();
    }
}
