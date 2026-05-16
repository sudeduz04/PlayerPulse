<?php

namespace App\Policies;

use App\Models\Matches;
use App\Models\User;
use App\Services\Authorization\TeamAccess;

class MatchPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER, User::ROLE_COACH], true);
    }

    public function view(User $user, Matches $match): bool
    {
        return app(TeamAccess::class)->canAccessTeam($user, $match->team_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_COACH], true);
    }

    public function update(User $user, Matches $match): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_COACH], true)
            && app(TeamAccess::class)->canAccessTeam($user, $match->team_id);
    }

    public function delete(User $user, Matches $match): bool
    {
        return $this->update($user, $match);
    }
}
