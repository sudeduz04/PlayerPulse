<?php

namespace App\Services;

use App\Models\Players;
use App\Models\PlayerTrainingPerformances;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Support\Collection;

class TrainingPerformanceService
{
    public function __construct(
        protected TrainingService $trainingService,
        private TeamAccess $teamAccess
    ) {}

    public function listByTraining(int $trainingId, User $user): Collection
    {
        $training = $this->trainingService->show($trainingId, $user);

        return $training->performances->load('player.position');
    }

    public function upsert(int $trainingId, array $data, User $user): PlayerTrainingPerformances
    {
        $training = $this->trainingService->show($trainingId, $user);
        $player = Players::findOrFail($data['player_id']);
        $this->teamAccess->assertTrainingPlayer($training, $player);

        return PlayerTrainingPerformances::updateOrCreate(
            [
                'training_id' => $trainingId,
                'player_id' => $data['player_id'],
            ],
            collect($data)->except('player_id')->toArray()
        );
    }

    public function bulkUpsert(int $trainingId, array $players, User $user): Collection
    {
        $training = $this->trainingService->show($trainingId, $user);

        $results = new \Illuminate\Database\Eloquent\Collection;

        foreach ($players as $playerData) {
            $player = Players::findOrFail($playerData['player_id']);
            $this->teamAccess->assertTrainingPlayer($training, $player);

            $performance = PlayerTrainingPerformances::updateOrCreate(
                [
                    'training_id' => $trainingId,
                    'player_id' => $playerData['player_id'],
                ],
                collect($playerData)->except('player_id')->toArray()
            );

            $results->push($performance);
        }

        return $results->load('player.position');
    }
}
