<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Lineup\StoreLineupRequest;
use App\Services\LineupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LineupController extends BaseController
{
    public function __construct(private LineupService $lineupService) {}

    public function index(Request $request): JsonResponse
    {
        $lineups = $this->lineupService->list($request->query(), $request->user());

        return $this->sendResponse($lineups, 'Lineups retrieved successfully.');
    }

    public function store(StoreLineupRequest $request): JsonResponse
    {
        $lineup = $this->lineupService->create($request->validated(), $request->user());

        return $this->sendResponse($lineup, 'Lineup created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $lineup = $this->lineupService->show($id, $request->user());

        return $this->sendResponse($lineup, 'Lineup retrieved successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->lineupService->delete($id, $request->user());

        return $this->sendResponse(null, 'Lineup deleted successfully.');
    }

    public function options(Request $request): JsonResponse
    {
        return $this->sendResponse([
            'matches' => $this->lineupService->availableMatches($request->user()),
            'positions' => $this->lineupService->positions(),
        ], 'Lineup options retrieved successfully.');
    }

    public function roster(Request $request, int $matchId): JsonResponse
    {
        $roster = $this->lineupService->rosterForMatch($matchId, $request->user());

        return $this->sendResponse($roster, 'Match roster retrieved successfully.');
    }
}
