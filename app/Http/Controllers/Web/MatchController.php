<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Match\StoreMatchRequest;
use App\Http\Requests\Web\Match\UpdateMatchRequest;
use App\Models\Teams;
use App\Services\MatchService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected MatchService $matchService) {}

    public function index(Request $request)
    {
        $matches = $this->matchService->list($request->query(), $request->user());

        return view('matches.index', [
            'matches' => $matches,
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $match = $this->matchService->show($id, $request->user());

        return view('matches.show', [
            'match' => $match,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        return view('matches.create', [
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreMatchRequest $request)
    {
        $match = $this->matchService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.matches.show', $match->id)
            ->with('success', 'Maç başarıyla oluşturuldu.');
    }

    public function edit(Request $request, int $id)
    {
        $match = $this->matchService->show($id, $request->user());

        return view('matches.edit', [
            'match' => $match,
            'teams' => $this->getTeamsForUser($request->user()),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdateMatchRequest $request, int $id)
    {
        $this->matchService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.matches.show', $id)
            ->with('success', 'Maç başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->matchService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.matches.index')
            ->with('success', 'Maç başarıyla silindi.');
    }

    private function getTeamsForUser($user)
    {
        if ($user->isSuperAdmin()) {
            return Teams::all();
        }

        return Teams::whereIn('id', $user->getTeamIds())->get();
    }
}
