<?php

namespace App\Http\Controllers\Api;

use App\Models\Leagues;
use App\Services\FixtureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeagueController extends BaseController
{
    public function __construct(private FixtureService $fixtureService) {}

    public function index(Request $request): JsonResponse
    {
        $leagues = $this->fixtureService->list($request->query());

        return $this->sendResponse($leagues, 'Leagues retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'season' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['integer', 'exists:teams,id'],
        ]);

        $league = $this->fixtureService->createLeague($data);

        return $this->sendResponse($league, 'League created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $league = $this->fixtureService->show($id);

        return $this->sendResponse($league, 'League retrieved successfully.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'season' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['integer', 'exists:teams,id'],
        ]);

        $league = $this->fixtureService->updateLeague($id, $data);

        return $this->sendResponse($league, 'League updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->fixtureService->deleteLeague($id);

        return $this->sendResponse(null, 'League deleted successfully.');
    }
}
