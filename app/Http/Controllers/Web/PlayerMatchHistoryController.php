<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MatchStatsService;
use Illuminate\Http\Request;

class PlayerMatchHistoryController extends Controller
{
    public function __construct(private MatchStatsService $statsService) {}

    public function index(Request $request)
    {
        $history = $this->statsService->historyForPlayer($request->user(), $request->query());

        return view('player.matches.index', [
            'summary' => $history['summary'],
            'stats' => $history['stats'],
            'filters' => $request->query(),
        ]);
    }
}
