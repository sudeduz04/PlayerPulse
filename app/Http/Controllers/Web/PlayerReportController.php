<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DevelopmentReportService;
use Illuminate\Http\Request;

class PlayerReportController extends Controller
{
    public function __construct(private DevelopmentReportService $reportService) {}

    public function index(Request $request)
    {
        $reports = $this->reportService->listForCurrentPlayer($request->user(), $request->query());
        $summary = $this->reportService->summary($request->query(), $request->user());

        return view('player.reports.index', [
            'reports' => $reports,
            'summary' => $summary,
            'filters' => $request->query(),
        ]);
    }
}
