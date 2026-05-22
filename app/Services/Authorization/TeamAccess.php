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

    public function canAccessMatch(User $user, Matches $match): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isRole(User::ROLE_PLAYER)) {
            return $user->player && $match->involvesTeam($user->player->team_id);
        }

        if ($user->isRole(User::ROLE_MANAGER) || $user->isRole(User::ROLE_COACH)) {
            return $user->getTeamIds()->contains(fn ($id) => $match->involvesTeam((int) $id));
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

        abort_unless($this->canAccessTeam($user, $teamId), 403, 'Bu takima erisim yetkiniz yok.');
    }

    public function assertMatch(User $user, Matches $match): void
    {
        abort_unless($this->canAccessMatch($user, $match), 403, 'Bu maca erisim yetkiniz yok.');
    }

    public function assertPlayer(User $user, Players $player): void
    {
        abort_unless($this->canAccessPlayer($user, $player), 403, 'Bu oyuncuya erisim yetkiniz yok.');
    }

    public function assertTrainingPlayer(Trainings $training, Players $player): void
    {
        abort_unless(
            $training->team_id === $player->team_id,
            422,
            'Oyuncu bu antrenmanin takimina ait degil.'
        );
    }

    public function assertMatchPlayer(Matches $match, Players $player): void
    {
        abort_unless(
            $match->involvesTeam($player->team_id),
            422,
            'Oyuncu bu macin takimlarina ait degil.'
        );
    }
}
