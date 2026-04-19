<?php

namespace App\Services;

use App\Models\PhysicalMeasurements;
use App\Models\Players;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PhysicalMeasurementService
{
    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return PhysicalMeasurements::where('player_id', $player->id)
            ->latest('measurement_date')
            ->paginate(15);
    }

    public function create(array $data, User $user): PhysicalMeasurements
    {
        $this->authorizedPlayer($data['player_id'], $user);

        return PhysicalMeasurements::create($data);
    }

    public function update(int $id, array $data, User $user): PhysicalMeasurements
    {
        $measurement = PhysicalMeasurements::findOrFail($id);
        $this->authorizedPlayer($measurement->player_id, $user);
        $measurement->update($data);

        return $measurement->fresh('player');
    }

    public function delete(int $id, User $user): void
    {
        $measurement = PhysicalMeasurements::findOrFail($id);
        $this->authorizedPlayer($measurement->player_id, $user);
        $measurement->delete();
    }

    private function authorizedPlayer(int $playerId, User $user): Players
    {
        $player = Players::findOrFail($playerId);

        if ($user->isRole('coach') || $user->isRole('manager')) {
            if (! $user->getTeamIds()->contains($player->team_id)) {
                abort(403, 'Bu oyuncuya erişim yetkiniz yok.');
            }
        }

        return $player;
    }
}
