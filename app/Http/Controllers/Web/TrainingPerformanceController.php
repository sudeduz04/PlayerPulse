<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Training\BulkPerformanceRequest;
use App\Models\Players;
use App\Services\TrainingPerformanceService;
use App\Services\TrainingService;
use Illuminate\Http\Request;

class TrainingPerformanceController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        protected TrainingPerformanceService $performanceService,
        protected TrainingService $trainingService,
    ) {}

    public function edit(Request $request, int $trainingId)
    {
        $training = $this->trainingService->show($trainingId, $request->user());

        $teamPlayers = Players::with('position')
            ->where('team_id', $training->team_id)
            ->where('status', 'active')
            ->orderBy('jersey_number')
            ->get();

        $existingPerformances = $training->performances->keyBy('player_id');

        return view('trainings.performances', [
            'training' => $training,
            'teamPlayers' => $teamPlayers,
            'existingPerformances' => $existingPerformances,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(BulkPerformanceRequest $request, int $trainingId)
    {
        $this->performanceService->bulkUpsert(
            $trainingId,
            $request->validated()['players'],
            $request->user()
        );

        return redirect()
            ->route($this->routePrefix().'.trainings.show', $trainingId)
            ->with('success', 'Performans kayıtları başarıyla güncellendi.');
    }
}
