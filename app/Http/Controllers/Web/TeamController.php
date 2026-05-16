<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Team\AssignCoachRequest;
use App\Http\Requests\Web\Team\StoreTeamRequest;
use App\Http\Requests\Web\Team\UpdateTeamRequest;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected TeamService $teamService) {}

    public function index(Request $request)
    {
        $teams = $this->teamService->list($request->query(), $request->user());

        return view('teams.index', [
            'teams' => $teams,
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $team = $this->teamService->show($id, $request->user());

        $coaches = $request->user()->isRole('super_admin')
            ? User::where('role', 'coach')->get()
            : collect();

        $managers = $request->user()->isRole('super_admin')
            ? User::where('role', 'manager')->get()
            : collect();

        return view('teams.show', [
            'team' => $team,
            'coaches' => $coaches,
            'managers' => $managers,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create()
    {
        return view('teams.create', [
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreTeamRequest $request)
    {
        $team = $this->teamService->create($request->validated());

        return redirect()
            ->route($this->routePrefix().'.teams.show', $team->id)
            ->with('success', 'Takım başarıyla oluşturuldu.');
    }

    public function edit(Request $request, int $id)
    {
        $team = $this->teamService->show($id, $request->user());

        return view('teams.edit', [
            'team' => $team,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdateTeamRequest $request, int $id)
    {
        $this->teamService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.teams.show', $id)
            ->with('success', 'Takım başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->teamService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.teams.index')
            ->with('success', 'Takım başarıyla silindi.');
    }

    public function assignStaff(AssignCoachRequest $request, int $teamId)
    {
        $this->teamService->assignStaff($teamId, $request->validated()['user_id']);

        return redirect()
            ->back()
            ->with('success', 'Kullanıcı takıma başarıyla atandı.');
    }

    public function removeStaff(int $teamId, int $userId)
    {
        $this->teamService->removeStaff($teamId, $userId);

        return redirect()
            ->back()
            ->with('success', 'Kullanıcı takımdan başarıyla çıkarıldı.');
    }
}
