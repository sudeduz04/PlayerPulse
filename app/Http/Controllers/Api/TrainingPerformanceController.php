<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Training\BulkPerformanceRequest;
use App\Http\Requests\Training\StorePerformanceRequest;
use App\Jobs\ProcessBulkPerformanceJob;
use App\Services\TrainingPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $players = $request->validated()['players'];

        if ($request->boolean('async') && count($players) > 30) {
            $uuid = (string) Str::uuid();
            Cache::put('bulk:'.$uuid, [
                'status' => 'queued',
                'processed' => 0,
                'total' => count($players),
            ], now()->addHour());

            ProcessBulkPerformanceJob::dispatch($uuid, $trainingId, $players, $request->user()->id);

            return $this->sendResponse([
                'job_id' => $uuid,
                'status' => 'queued',
                'status_url' => route('api.jobs.status', $uuid),
                'total' => count($players),
            ], 'Bulk performance import queued.', 202);
        }

        $performances = $this->performanceService->bulkUpsert(
            $trainingId,
            $players,
            $request->user()
        );

        return $this->sendResponse($performances, 'Performances saved successfully.', 201);
    }
}
