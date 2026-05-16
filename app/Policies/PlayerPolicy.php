<?php

namespace App\Policies;

use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;

class PlayerPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER, User::ROLE_COACH], true);
    }

    public function view(User $user, Players $player): bool
    {
        return app(TeamAccess::class)->canAccessPlayer($user, $player);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER, User::ROLE_COACH], true);
    }

    public function update(User $user, Players $player): bool
    {
        return ! $user->isRole(User::ROLE_PLAYER) && app(TeamAccess::class)->canAccessPlayer($user, $player);
    }

    public function delete(User $user, Players $player): bool
    {
        return ! $user->isRole(User::ROLE_PLAYER) && app(TeamAccess::class)->canAccessPlayer($user, $player);
    }

    public function createAccount(User $user, Players $player): bool
    {
        return in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_MANAGER], true)
            && app(TeamAccess::class)->canAccessPlayer($user, $player);
    }
}
