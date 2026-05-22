<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SmartLineup\StoreSmartLineupRequest;
use App\Services\LineupService;
use App\Services\SmartLineupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SmartLineupController extends BaseController
{
    public function __construct(
        private SmartLineupService $smartLineup,
        private LineupService $lineupService,
    ) {}

    public function options(Request $request): JsonResponse
    {
        return $this->sendResponse([
            'matches' => $this->lineupService->availableMatches($request->user()),
            'ai_ready' => $this->smartLineup->isAiReady(),
            'ai_provider' => $this->smartLineup->aiProviderName(),
        ], 'Smart lineup options retrieved successfully.');
    }

    public function store(StoreSmartLineupRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $lineup = $this->smartLineup->suggestAndStore(
                (int) $data['match_id'],
                $data['formation'],
                $request->user(),
                $data['note'] ?? null,
            );
        } catch (Throwable $e) {
            return $this->sendError('AI lineup suggestion failed: '.$e->getMessage(), 400);
        }

        return $this->sendResponse($lineup, 'AI lineup suggestion created successfully.', 201);
    }
}
