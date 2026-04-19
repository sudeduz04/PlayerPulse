<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Match\BulkMatchStatsRequest;
use App\Http\Requests\Match\StoreMatchStatsRequest;
use App\Services\MatchStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $stats = $this->statsService->bulkUpsert(
            $matchId,
            $request->validated()['players'],
            $request->user()
        );

        return $this->sendResponse($stats, 'Match stats saved successfully.', 201);
    }
}
