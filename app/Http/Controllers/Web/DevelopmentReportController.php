<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Player\StoreDevelopmentReportRequest;
use App\Services\DevelopmentReportService;
use Illuminate\Http\Request;

class DevelopmentReportController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected DevelopmentReportService $reportService) {}

    public function store(StoreDevelopmentReportRequest $request, int $playerId)
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $this->reportService->create($data, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Gelişim raporu başarıyla eklendi.');
    }

    public function destroy(Request $request, int $playerId, int $id)
    {
        $this->reportService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Gelişim raporu başarıyla silindi.');
    }
}
