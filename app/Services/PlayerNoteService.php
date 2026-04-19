<?php

namespace App\Services;

use App\Models\PlayerNotes;
use App\Models\Players;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlayerNoteService
{
    public function listByPlayer(int $playerId, User $user): LengthAwarePaginator
    {
        $player = $this->authorizedPlayer($playerId, $user);

        return PlayerNotes::with('author')
            ->where('player_id', $player->id)
            ->latest('note_date')
            ->paginate(15);
    }

    public function create(array $data, User $user): PlayerNotes
    {
        $this->authorizedPlayer($data['player_id'], $user);
        $data['user_id'] = $user->id;

        return PlayerNotes::create($data);
    }

    public function delete(int $id, User $user): void
    {
        $note = PlayerNotes::findOrFail($id);
        $this->authorizedPlayer($note->player_id, $user);
        $note->delete();
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
