<?php

namespace App\Http\Controllers\Api;

use App\Services\MatchStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerMatchHistoryController extends BaseController
{
    public function __construct(private MatchStatsService $statsService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'match_type' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $history = $this->statsService->historyForPlayer($request->user(), $filters);

        return $this->sendResponse($history, 'Player match history retrieved successfully.');
    }
}
