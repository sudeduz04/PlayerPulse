<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Player\StoreNoteRequest;
use App\Services\PlayerNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerNoteController extends BaseController
{
    public function __construct(private PlayerNoteService $noteService) {}

    public function index(Request $request, int $playerId): JsonResponse
    {
        $notes = $this->noteService->listByPlayer($playerId, $request->user());

        return $this->sendResponse($notes, 'Notes retrieved successfully.');
    }

    public function store(StoreNoteRequest $request, int $playerId): JsonResponse
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $note = $this->noteService->create($data, $request->user());

        return $this->sendResponse($note, 'Note created successfully.', 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->noteService->delete($id, $request->user());

        return $this->sendResponse(null, 'Note deleted successfully.');
    }
}
