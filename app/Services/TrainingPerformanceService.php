<?php

namespace App\Services;

use App\Models\Players;
use App\Models\PlayerTrainingPerformances;
use App\Models\Trainings;
use App\Models\User;
use App\Services\Authorization\TeamAccess;
use Illuminate\Database\Eloquent\Builder;
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

    public function historyForPlayer(User $user, array $filters = []): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return [
                'summary' => $this->emptySummary(),
                'performances' => PlayerTrainingPerformances::query()
                    ->whereRaw('1 = 0')
                    ->paginate($this->perPage($filters)),
            ];
        }

        $query = $this->playerHistoryQuery($player->id, $filters);

        return [
            'summary' => $this->buildSummary($query),
            'performances' => $query
                ->with(['training.team', 'player.position'])
                ->orderByDesc(
                    Trainings::select('training_date')
                        ->whereColumn('trainings.id', 'player_training_performances.training_id')
                        ->limit(1)
                )
                ->paginate($this->perPage($filters)),
        ];
    }

    public function recentHistoryForPlayer(User $user, int $limit = 5): Collection
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return collect();
        }

        return $this->playerHistoryQuery($player->id, [])
            ->with(['training.team'])
            ->orderByDesc(
                Trainings::select('training_date')
                    ->whereColumn('trainings.id', 'player_training_performances.training_id')
                    ->limit(1)
            )
            ->limit($limit)
            ->get();
    }

    public function summaryForPlayer(User $user, array $filters = []): array
    {
        abort_unless($user->isRole(User::ROLE_PLAYER), 403, 'Bu işlem yalnızca oyuncular içindir.');

        $player = $user->player;

        if (! $player) {
            return $this->emptySummary();
        }

        return $this->buildSummary($this->playerHistoryQuery($player->id, $filters));
    }

    private function playerHistoryQuery(int $playerId, array $filters): Builder
    {
        $query = PlayerTrainingPerformances::query()
            ->where('player_id', $playerId)
            ->whereHas('training');

        if (! empty($filters['attendance_status'])) {
            $query->where('attendance_status', $filters['attendance_status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereHas('training', fn (Builder $q) => $q->where('training_date', '>=', $filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereHas('training', fn (Builder $q) => $q->where('training_date', '<=', $filters['date_to']));
        }

        return $query;
    }

    private function buildSummary(Builder $query): array
    {
        $total = (clone $query)->count();
        $attended = (clone $query)->where('attendance_status', 'attended')->count();
        $absent = (clone $query)->where('attendance_status', 'absent')->count();
        $excused = (clone $query)->where('attendance_status', 'excused')->count();
        $averageScore = (clone $query)->whereNotNull('performance_score')->avg('performance_score');

        return [
            'total_trainings' => $total,
            'attended' => $attended,
            'absent' => $absent,
            'excused' => $excused,
            'attendance_rate' => $total > 0 ? round(($attended / $total) * 100, 2) : 0.0,
            'average_score' => $averageScore !== null ? round((float) $averageScore, 2) : null,
        ];
    }

    private function emptySummary(): array
    {
        return [
            'total_trainings' => 0,
            'attended' => 0,
            'absent' => 0,
            'excused' => 0,
            'attendance_rate' => 0.0,
            'average_score' => null,
        ];
    }

    private function perPage(array $filters): int
    {
        return max(1, min((int) ($filters['per_page'] ?? 15), 100));
    }
}
