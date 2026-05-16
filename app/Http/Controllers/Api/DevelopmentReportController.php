<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\DevelopmentReport\StoreDevelopmentReportRequest;
use App\Services\DevelopmentReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevelopmentReportController extends BaseController
{
    public function __construct(private DevelopmentReportService $reportService) {}

    public function index(Request $request): JsonResponse
    {
        $reports = $this->reportService->list($request->query(), $request->user());

        return $this->sendResponse([
            'summary' => $this->reportService->summary($request->query(), $request->user()),
            'reports' => $reports,
        ], 'Development reports retrieved successfully.');
    }

    public function myReports(Request $request): JsonResponse
    {
        $reports = $this->reportService->listForCurrentPlayer($request->user(), $request->query());

        return $this->sendResponse([
            'summary' => $this->reportService->summary($request->query(), $request->user()),
            'reports' => $reports,
        ], 'Player development reports retrieved successfully.');
    }

    public function store(StoreDevelopmentReportRequest $request, int $playerId): JsonResponse
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $report = $this->reportService->create($data, $request->user());

        return $this->sendResponse($report, 'Development report created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $report = $this->reportService->show($id, $request->user());

        return $this->sendResponse($report, 'Development report retrieved successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->reportService->delete($id, $request->user());

        return $this->sendResponse(null, 'Development report deleted successfully.');
    }
}
