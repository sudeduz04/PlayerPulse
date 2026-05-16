<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Lineup\StoreLineupRequest;
use App\Services\LineupService;
use Illuminate\Http\Request;

class LineupController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private LineupService $lineupService) {}

    public function index(Request $request)
    {
        $lineups = $this->lineupService->list($request->query(), $request->user());

        return view('lineups.index', [
            'lineups' => $lineups,
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function create(Request $request)
    {
        $matches = $this->lineupService->availableMatches($request->user());
        $positions = $this->lineupService->positions();

        $matchId = (int) $request->query('match_id');
        $roster = collect();

        if ($matchId) {
            $roster = $this->lineupService->rosterForMatch($matchId, $request->user());
        }

        return view('lineups.create', [
            'matches' => $matches,
            'positions' => $positions,
            'roster' => $roster,
            'selectedMatchId' => $matchId ?: null,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StoreLineupRequest $request)
    {
        $lineup = $this->lineupService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.lineups.show', $lineup->id)
            ->with('success', 'Kadro başarıyla oluşturuldu.');
    }

    public function show(Request $request, int $id)
    {
        $lineup = $this->lineupService->show($id, $request->user());

        return view('lineups.show', [
            'lineup' => $lineup,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->lineupService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.lineups.index')
            ->with('success', 'Kadro silindi.');
    }
}
