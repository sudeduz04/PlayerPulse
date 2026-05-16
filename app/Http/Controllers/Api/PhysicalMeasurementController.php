<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Player\StoreMeasurementRequest;
use App\Services\PhysicalMeasurementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhysicalMeasurementController extends BaseController
{
    public function __construct(private PhysicalMeasurementService $measurementService) {}

    public function index(Request $request): JsonResponse
    {
        $measurements = $this->measurementService->list($request->query(), $request->user());

        return $this->sendResponse([
            'summary' => $this->measurementService->summary($request->query(), $request->user()),
            'measurements' => $measurements,
        ], 'Physical measurements retrieved successfully.');
    }

    public function store(StoreMeasurementRequest $request, int $playerId): JsonResponse
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $measurement = $this->measurementService->create($data, $request->user());

        return $this->sendResponse($measurement, 'Physical measurement created successfully.', 201);
    }

    public function update(StoreMeasurementRequest $request, int $id): JsonResponse
    {
        $measurement = $this->measurementService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($measurement, 'Physical measurement updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->measurementService->delete($id, $request->user());

        return $this->sendResponse(null, 'Physical measurement deleted successfully.');
    }
}
