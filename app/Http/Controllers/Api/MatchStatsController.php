<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Match\BulkMatchStatsRequest;
use App\Http\Requests\Match\StoreMatchStatsRequest;
use App\Jobs\ProcessBulkMatchStatsJob;
use App\Services\MatchStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MatchStatsController extends BaseController
{
    public function __construct(protected MatchStatsService $statsService) {}

    public function index(Request $request, int $matchId): JsonResponse
    {
        $stats = $this->statsService->listByMatch($matchId, $request->user());

        return $this->sendResponse($stats, 'Match stats retrieved successfully.');
    }

    public function store(StoreMatchStatsRequest $request, int $matchId): JsonResponse
    {
        $stat = $this->statsService->upsert($matchId, $request->validated(), $request->user());

        return $this->sendResponse($stat->load('player.position'), 'Match stat saved successfully.', 201);
    }

    public function bulkStore(BulkMatchStatsRequest $request, int $matchId): JsonResponse
    {
        $players = $request->validated()['players'];

        if ($request->boolean('async') && count($players) > 30) {
            $uuid = (string) Str::uuid();
            Cache::put('bulk:'.$uuid, [
                'status' => 'queued',
                'processed' => 0,
                'total' => count($players),
            ], now()->addHour());

            ProcessBulkMatchStatsJob::dispatch($uuid, $matchId, $players, $request->user()->id);

            return $this->sendResponse([
                'job_id' => $uuid,
                'status' => 'queued',
                'status_url' => route('api.jobs.status', $uuid),
                'total' => count($players),
            ], 'Bulk match stats import queued.', 202);
        }

        $stats = $this->statsService->bulkUpsert(
            $matchId,
            $players,
            $request->user()
        );

        return $this->sendResponse($stats, 'Match stats saved successfully.', 201);
    }
}
