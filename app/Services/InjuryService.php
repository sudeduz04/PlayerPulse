<?php

namespace App\Services;

use App\Models\Injuries;
use App\Models\Players;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InjuryService
{
    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return Injuries::where('player_id', $player->id)
            ->latest('start_date')
            ->paginate(15);
    }

    public function show(int $id, User $user): Injuries
    {
        $injury = Injuries::with('player.team')->findOrFail($id);
        $this->authorizePlayerAccess($user, $injury->player);

        return $injury;
    }

    public function create(array $data, User $user): Injuries
    {
        $this->authorizedPlayer($data['player_id'], $user);

        return Injuries::create($data);
    }

    public function update(int $id, array $data, User $user): Injuries
    {
        $injury = Injuries::findOrFail($id);
        $this->authorizedPlayer($injury->player_id, $user);
        $injury->update($data);

        return $injury->fresh('player');
    }

    public function delete(int $id, User $user): void
    {
        $injury = Injuries::findOrFail($id);
        $this->authorizedPlayer($injury->player_id, $user);
        $injury->delete();
    }

    private function authorizedPlayer(int $playerId, User $user): Players
    {
        $player = Players::with('team')->findOrFail($playerId);
        $this->authorizePlayerAccess($user, $player);

        return $player;
    }

    private function authorizePlayerAccess(User $user, Players $player): void
    {
        if ($user->isRole('coach') || $user->isRole('manager')) {
            if (! $user->getTeamIds()->contains($player->team_id)) {
                abort(403, 'Bu oyuncuya erişim yetkiniz yok.');
            }
        }
    }
}
