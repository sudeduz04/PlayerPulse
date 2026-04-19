<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Training\BulkPerformanceRequest;
use App\Http\Requests\Training\StorePerformanceRequest;
use App\Services\TrainingPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingPerformanceController extends BaseController
{
    public function __construct(protected TrainingPerformanceService $performanceService) {}

    public function index(Request $request, int $trainingId): JsonResponse
    {
        $performances = $this->performanceService->listByTraining($trainingId, $request->user());

        return $this->sendResponse($performances, 'Performances retrieved successfully.');
    }

    public function store(StorePerformanceRequest $request, int $trainingId): JsonResponse
    {
        $performance = $this->performanceService->upsert($trainingId, $request->validated(), $request->user());

        return $this->sendResponse($performance->load('player.position'), 'Performance saved successfully.', 201);
    }

    public function bulkStore(BulkPerformanceRequest $request, int $trainingId): JsonResponse
    {
        $performances = $this->performanceService->bulkUpsert(
            $trainingId,
            $request->validated()['players'],
            $request->user()
        );

        return $this->sendResponse($performances, 'Performances saved successfully.', 201);
    }
}
