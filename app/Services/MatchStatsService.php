<?php

namespace App\Services;

use App\Models\PlayerMatchStats;
use App\Models\Players;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Database\Eloquent\Collection;

class MatchStatsService
{
    public function __construct(
        protected MatchService $matchService,
        private TeamAccess $teamAccess
    ) {}

    public function listByMatch(int $matchId, User $user): Collection
    {
        $match = $this->matchService->show($matchId, $user);

        return $match->playerMatchStats->load('player.position');
    }

    public function upsert(int $matchId, array $data, User $user): PlayerMatchStats
    {
        $match = $this->matchService->show($matchId, $user);
        $player = Players::findOrFail($data['player_id']);
        $this->teamAccess->assertMatchPlayer($match, $player);

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
        $match = $this->matchService->show($matchId, $user);

        $results = new Collection;

        foreach ($players as $playerData) {
            $player = Players::findOrFail($playerData['player_id']);
            $this->teamAccess->assertMatchPlayer($match, $player);

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
