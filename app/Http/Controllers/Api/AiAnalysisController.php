<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AiAnalysis\StoreAnalysisRequest;
use App\Services\AiAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AiAnalysisController extends BaseController
{
    public function __construct(private AiAnalysisService $service) {}

    public function index(Request $request): JsonResponse
    {
        $analyses = $this->service->list($request->query(), $request->user());

        return $this->sendResponse($analyses, 'AI analyses retrieved successfully.');
    }

    public function options(Request $request): JsonResponse
    {
        return $this->sendResponse([
            'players' => $this->service->availablePlayers($request->user()),
            'ai_ready' => $this->service->isAiReady(),
            'ai_provider' => $this->service->aiProviderName(),
        ], 'AI analysis options retrieved successfully.');
    }

    public function store(StoreAnalysisRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $analysis = $this->service->analyzePlayer(
                (int) $data['player_id'],
                $request->user(),
                $data['focus'] ?? null,
            );
        } catch (Throwable $e) {
            return $this->sendError('AI analysis failed: '.$e->getMessage(), 400);
        }

        return $this->sendResponse($analysis, 'AI analysis created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $analysis = $this->service->show($id, $request->user());

        return $this->sendResponse($analysis, 'AI analysis retrieved successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->service->delete($id, $request->user());

        return $this->sendResponse(null, 'AI analysis deleted successfully.');
    }
}
