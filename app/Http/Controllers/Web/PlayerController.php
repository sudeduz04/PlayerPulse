<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Player\StorePlayerRequest;
use App\Http\Requests\Web\Player\UpdatePlayerRequest;
use App\Models\Positions;
use App\Models\Teams;
use App\Services\PlayerService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function __construct(protected PlayerService $playerService) {}

    public function index(Request $request)
    {
        $players = $this->playerService->list($request->query(), $request->user());

        return view('players.index', [
            'players' => $players,
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $player = $this->playerService->show($id, $request->user());

        return view('players.show', [
            'player' => $player,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        return view('players.create', [
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StorePlayerRequest $request)
    {
        $player = $this->playerService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix() . '.players.show', $player->id)
            ->with('success', 'Oyuncu başarıyla oluşturuldu.');
    }

    public function edit(Request $request, int $id)
    {
        $player = $this->playerService->show($id, $request->user());

        return view('players.edit', [
            'player' => $player,
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdatePlayerRequest $request, int $id)
    {
        $this->playerService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix() . '.players.show', $id)
            ->with('success', 'Oyuncu başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->playerService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix() . '.players.index')
            ->with('success', 'Oyuncu başarıyla silindi.');
    }

    private function getTeamsForUser($user)
    {
        if ($user->isRole('coach')) {
            return Teams::whereIn('id', $user->getTeamIds())->get();
        }

        return Teams::all();
    }

    private function routePrefix(): string
    {
        return auth()->user()->isRole('manager') ? 'manager' : 'coach';
    }
}
