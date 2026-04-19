<?php

namespace App\Services;

use App\Models\PlayerTrainingPerformances;
use App\Models\User;
use Illuminate\Support\Collection;

class TrainingPerformanceService
{
    public function __construct(protected TrainingService $trainingService) {}

    public function listByTraining(int $trainingId, User $user): Collection
    {
        $training = $this->trainingService->show($trainingId, $user);

        return $training->performances->load('player.position');
    }

    public function upsert(int $trainingId, array $data, User $user): PlayerTrainingPerformances
    {
        $this->trainingService->show($trainingId, $user);

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
        $this->trainingService->show($trainingId, $user);

        $results = collect();

        foreach ($players as $playerData) {
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
