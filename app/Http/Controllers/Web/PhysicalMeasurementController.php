<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Player\StoreMeasurementRequest;
use App\Services\PhysicalMeasurementService;
use Illuminate\Http\Request;

class PhysicalMeasurementController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected PhysicalMeasurementService $measurementService) {}

    public function store(StoreMeasurementRequest $request, int $playerId)
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $this->measurementService->create($data, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Fiziksel ölçüm başarıyla eklendi.');
    }

    public function destroy(Request $request, int $playerId, int $id)
    {
        $this->measurementService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Fiziksel ölçüm başarıyla silindi.');
    }
}
