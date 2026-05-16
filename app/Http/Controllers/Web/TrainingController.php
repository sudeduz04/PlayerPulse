<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Training\StoreTrainingRequest;
use App\Http\Requests\Web\Training\UpdateTrainingRequest;
use App\Models\Teams;
use App\Services\TrainingService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected TrainingService $trainingService) {}

    public function index(Request $request)
    {
        $trainings = $this->trainingService->list($request->query(), $request->user());

        return view('trainings.index', [
            'trainings' => $trainings,
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $training = $this->trainingService->show($id, $request->user());

        return view('trainings.show', [
            'training' => $training,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        return view('trainings.create', [
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.trainings.show', $training->id)
            ->with('success', 'Antrenman başarıyla oluşturuldu.');
    }

    public function edit(Request $request, int $id)
    {
        $training = $this->trainingService->show($id, $request->user());

        return view('trainings.edit', [
            'training' => $training,
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdateTrainingRequest $request, int $id)
    {
        $this->trainingService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.trainings.show', $id)
            ->with('success', 'Antrenman başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->trainingService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.trainings.index')
            ->with('success', 'Antrenman başarıyla silindi.');
    }

    private function getTeamsForUser($user)
    {
        if ($user->isSuperAdmin()) {
            return Teams::all();
        }

        return Teams::whereIn('id', $user->getTeamIds())->get();
    }
}
