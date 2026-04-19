<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Match\StoreMatchRequest;
use App\Http\Requests\Match\UpdateMatchRequest;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends BaseController
{
    public function __construct(protected MatchService $matchService) {}

    public function index(Request $request): JsonResponse
    {
        $matches = $this->matchService->list($request->query(), $request->user());

        return $this->sendResponse($matches, 'Matches retrieved successfully.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $match = $this->matchService->show($id, $request->user());

        return $this->sendResponse($match, 'Match retrieved successfully.');
    }

    public function store(StoreMatchRequest $request): JsonResponse
    {
        $match = $this->matchService->create($request->validated(), $request->user());

        return $this->sendResponse($match, 'Match created successfully.', 201);
    }

    public function update(UpdateMatchRequest $request, int $id): JsonResponse
    {
        $match = $this->matchService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($match, 'Match updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->matchService->delete($id, $request->user());

        return $this->sendResponse(null, 'Match deleted successfully.');
    }
}
