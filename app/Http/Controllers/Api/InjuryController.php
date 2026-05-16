<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Player\StoreInjuryRequest;
use App\Services\InjuryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InjuryController extends BaseController
{
    public function __construct(private InjuryService $injuryService) {}

    public function index(Request $request): JsonResponse
    {
        $injuries = $this->injuryService->list($request->query(), $request->user());

        return $this->sendResponse([
            'summary' => $this->injuryService->summary($request->query(), $request->user()),
            'injuries' => $injuries,
        ], 'Injuries retrieved successfully.');
    }

    public function store(StoreInjuryRequest $request, int $playerId): JsonResponse
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $injury = $this->injuryService->create($data, $request->user());

        return $this->sendResponse($injury, 'Injury created successfully.', 201);
    }

    public function update(StoreInjuryRequest $request, int $id): JsonResponse
    {
        $injury = $this->injuryService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($injury, 'Injury updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->injuryService->delete($id, $request->user());

        return $this->sendResponse(null, 'Injury deleted successfully.');
    }
}
