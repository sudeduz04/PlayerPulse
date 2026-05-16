<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\DevelopmentReport\StoreEvaluationRequest;
use App\Services\DevelopmentReportService;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private DevelopmentReportService $reportService) {}

    public function index(Request $request)
    {
        $reports = $this->reportService->list($request->query(), $request->user());
        $summary = $this->reportService->summary($request->query(), $request->user());

        return view('evaluations.index', [
            'reports' => $reports,
            'summary' => $summary,
            'filters' => $request->query(),
            'players' => $this->reportService->playersForEvaluation($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        abort_if($request->user()->isRole('manager'), 403);

        return view('evaluations.create', [
            'players' => $this->reportService->playersForEvaluation($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $report = $this->reportService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.evaluations.show', $report->id)
            ->with('success', 'Değerlendirme başarıyla oluşturuldu.');
    }

    public function show(Request $request, int $id)
    {
        $report = $this->reportService->show($id, $request->user());

        return view('evaluations.show', [
            'report' => $report,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->reportService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.evaluations.index')
            ->with('success', 'Değerlendirme başarıyla silindi.');
    }
}
