<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Requests\Training\UpdateTrainingRequest;
use App\Services\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingController extends BaseController
{
    public function __construct(protected TrainingService $trainingService) {}

    public function index(Request $request): JsonResponse
    {
        $trainings = $this->trainingService->list($request->query(), $request->user());

        return $this->sendResponse($trainings, 'Trainings retrieved successfully.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $training = $this->trainingService->show($id, $request->user());

        return $this->sendResponse($training, 'Training retrieved successfully.');
    }

    public function store(StoreTrainingRequest $request): JsonResponse
    {
        $training = $this->trainingService->create($request->validated(), $request->user());

        return $this->sendResponse($training, 'Training created successfully.', 201);
    }

    public function update(UpdateTrainingRequest $request, int $id): JsonResponse
    {
        $training = $this->trainingService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($training, 'Training updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->trainingService->delete($id, $request->user());

        return $this->sendResponse(null, 'Training deleted successfully.');
    }
}
