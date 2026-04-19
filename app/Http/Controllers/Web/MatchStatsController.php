<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Match\BulkMatchStatsRequest;
use App\Models\Players;
use App\Services\MatchService;
use App\Services\MatchStatsService;
use Illuminate\Http\Request;

class MatchStatsController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        protected MatchStatsService $statsService,
        protected MatchService $matchService,
    ) {}

    public function edit(Request $request, int $matchId)
    {
        $match = $this->matchService->show($matchId, $request->user());

        $teamPlayers = Players::with('position')
            ->where('team_id', $match->team_id)
            ->where('status', 'active')
            ->orderBy('jersey_number')
            ->get();

        $existingStats = $match->playerMatchStats->keyBy('player_id');

        return view('matches.stats', [
            'match' => $match,
            'teamPlayers' => $teamPlayers,
            'existingStats' => $existingStats,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(BulkMatchStatsRequest $request, int $matchId)
    {
        $this->statsService->bulkUpsert(
            $matchId,
            $request->validated()['players'],
            $request->user()
        );

        return redirect()
            ->route($this->routePrefix().'.matches.show', $matchId)
            ->with('success', 'Maç istatistikleri başarıyla güncellendi.');
    }
}
