<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TrainingPerformanceService;
use Illuminate\Http\Request;

class PlayerTrainingHistoryController extends Controller
{
    public function __construct(private TrainingPerformanceService $performanceService) {}

    public function index(Request $request)
    {
        $history = $this->performanceService->historyForPlayer($request->user(), $request->query());

        return view('player.trainings.index', [
            'summary' => $history['summary'],
            'performances' => $history['performances'],
            'filters' => $request->query(),
        ]);
    }
}
