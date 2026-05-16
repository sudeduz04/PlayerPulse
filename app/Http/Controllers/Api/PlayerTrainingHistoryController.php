<?php

namespace App\Http\Controllers\Api;

use App\Services\TrainingPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerTrainingHistoryController extends BaseController
{
    public function __construct(private TrainingPerformanceService $performanceService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'attendance_status' => ['nullable', 'in:attended,absent,excused'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $history = $this->performanceService->historyForPlayer($request->user(), $filters);

        return $this->sendResponse($history, 'Player training history retrieved successfully.');
    }
}
