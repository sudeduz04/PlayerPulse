<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Player\StorePlayerRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends BaseController
{
    public function __construct(protected PlayerService $playerService) {}

    public function index(Request $request): JsonResponse
    {
        $players = $this->playerService->list($request->query(), $request->user());

        return $this->sendResponse($players, 'Players retrieved successfully.');
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        $player = $this->playerService->create($request->validated(), $request->user());

        return $this->sendResponse($player, 'Player created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $player = $this->playerService->show($id, $request->user());

        return $this->sendResponse($player, 'Player retrieved successfully.');
    }

    public function update(UpdatePlayerRequest $request, int $id): JsonResponse
    {
        $player = $this->playerService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($player, 'Player updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->playerService->delete($id, $request->user());

        return $this->sendResponse(null, 'Player deleted successfully.');
    }
}
