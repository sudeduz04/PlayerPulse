<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\InjuryService;
use App\Services\PhysicalMeasurementService;
use Illuminate\Http\Request;

class PlayerHealthController extends Controller
{
    public function __construct(
        private InjuryService $injuryService,
        private PhysicalMeasurementService $measurementService,
    ) {}

    public function index(Request $request)
    {
        return view('player.health.index', [
            'injurySummary' => $this->injuryService->summaryForPlayer($request->user()),
            'measurementSummary' => $this->measurementService->summaryForPlayer($request->user()),
            'injuries' => $this->injuryService->listForCurrentPlayer($request->user(), $request->query()),
            'measurements' => $this->measurementService->listForCurrentPlayer($request->user(), $request->query()),
            'filters' => $request->query(),
        ]);
    }
}
