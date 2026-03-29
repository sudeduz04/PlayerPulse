<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Team\AssignCoachRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
    public function __construct(protected TeamService $teamService) {}

    public function index(Request $request): JsonResponse
    {
        $teams = $this->teamService->list($request->query(), $request->user());

        return $this->sendResponse($teams, 'Teams retrieved successfully.');
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->create($request->validated());

        return $this->sendResponse($team, 'Team created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $team = $this->teamService->show($id, $request->user());

        return $this->sendResponse($team, 'Team retrieved successfully.');
    }

    public function update(UpdateTeamRequest $request, int $id): JsonResponse
    {
        $team = $this->teamService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($team, 'Team updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->teamService->delete($id, $request->user());

        return $this->sendResponse(null, 'Team deleted successfully.');
    }

    public function assignCoach(AssignCoachRequest $request, int $teamId): JsonResponse
    {
        $this->teamService->assignCoach($teamId, $request->validated()['user_id']);

        return $this->sendResponse(null, 'Coach assigned to team successfully.');
    }

    public function removeCoach(int $teamId, int $userId): JsonResponse
    {
        $this->teamService->removeCoach($teamId, $userId);

        return $this->sendResponse(null, 'Coach removed from team successfully.');
    }
}
