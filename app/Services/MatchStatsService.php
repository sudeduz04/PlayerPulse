<?php

namespace App\Services;

use App\Models\PlayerMatchStats;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MatchStatsService
{
    public function __construct(protected MatchService $matchService) {}

    public function listByMatch(int $matchId, User $user): Collection
    {
        $match = $this->matchService->show($matchId, $user);

        return $match->playerMatchStats->load('player.position');
    }

    public function upsert(int $matchId, array $data, User $user): PlayerMatchStats
    {
        $this->matchService->show($matchId, $user);

        return PlayerMatchStats::updateOrCreate(
            [
                'match_id' => $matchId,
                'player_id' => $data['player_id'],
            ],
            collect($data)->except('player_id')->toArray()
        );
    }

    public function bulkUpsert(int $matchId, array $players, User $user): Collection
    {
        $this->matchService->show($matchId, $user);

        $results = new Collection;

        foreach ($players as $playerData) {
            $stat = PlayerMatchStats::updateOrCreate(
                [
                    'match_id' => $matchId,
                    'player_id' => $playerData['player_id'],
                ],
                collect($playerData)->except('player_id')->toArray()
            );

            $results->push($stat);
        }

        return $results->load('player.position');
    }
}
