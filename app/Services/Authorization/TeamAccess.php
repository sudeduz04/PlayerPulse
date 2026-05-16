<?php

namespace App\Services\Authorization;

use App\Models\Matches;
use App\Models\Players;
use App\Models\Teams;
use App\Models\Trainings;
use App\Models\User;

class TeamAccess
{
    public function canAccessTeam(User $user, int $teamId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isRole(User::ROLE_MANAGER) || $user->isRole(User::ROLE_COACH)) {
            return $user->getTeamIds()->contains($teamId);
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            return $user->player?->team_id === $teamId;
        }

        return false;
    }

    public function canAccessPlayer(User $user, Players $player): bool
    {
        if ($user->isRole(User::ROLE_PLAYER)) {
            return $user->player?->id === $player->id;
        }

        return $this->canAccessTeam($user, $player->team_id);
    }

    public function assertTeam(User $user, Teams|int $team): void
    {
        $teamId = $team instanceof Teams ? $team->id : $team;

        abort_unless($this->canAccessTeam($user, $teamId), 403, 'Bu takıma erişim yetkiniz yok.');
    }

    public function assertPlayer(User $user, Players $player): void
    {
        abort_unless($this->canAccessPlayer($user, $player), 403, 'Bu oyuncuya erişim yetkiniz yok.');
    }

    public function assertTrainingPlayer(Trainings $training, Players $player): void
    {
        abort_unless(
            $training->team_id === $player->team_id,
            422,
            'Oyuncu bu antrenmanın takımına ait değil.'
        );
    }

    public function assertMatchPlayer(Matches $match, Players $player): void
    {
        abort_unless(
            $match->team_id === $player->team_id,
            422,
            'Oyuncu bu maçın takımına ait değil.'
        );
    }
}
