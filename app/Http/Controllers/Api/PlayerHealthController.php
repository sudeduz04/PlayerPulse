<?php

namespace App\Http\Controllers\Api;

use App\Services\InjuryService;
use App\Services\PhysicalMeasurementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerHealthController extends BaseController
{
    public function __construct(
        private InjuryService $injuryService,
        private PhysicalMeasurementService $measurementService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:ongoing,recovered'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return $this->sendResponse([
            'injury_summary' => $this->injuryService->summaryForPlayer($request->user()),
            'measurement_summary' => $this->measurementService->summaryForPlayer($request->user()),
            'injuries' => $this->injuryService->listForCurrentPlayer($request->user(), $filters),
            'measurements' => $this->measurementService->listForCurrentPlayer($request->user(), $filters),
        ], 'Player health data retrieved successfully.');
    }
}
