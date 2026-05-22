<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Services\LineupService;
use App\Services\SmartLineupService;
use Illuminate\Http\Request;
use Throwable;

class SmartLineupController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private SmartLineupService $smartLineup,
        private LineupService $lineupService,
    ) {}

    public function create(Request $request)
    {
        return view('smart_lineups.create', [
            'matches' => $this->lineupService->availableMatches($request->user()),
            'aiReady' => $this->smartLineup->isAiReady(),
            'aiProvider' => $this->smartLineup->aiProviderName(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'formation' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $lineup = $this->smartLineup->queueSuggestion((int) $data['match_id'], $data['formation'], $request->user(), $data['note'] ?? null);
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'AI onerisi baslatilamadi: '.$e->getMessage()], 400);
            }

            return redirect()->route($this->routePrefix().'.smart-squad.create')->with('error', 'AI onerisi baslatilamadi: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'AI kadro onerisi siraya alindi.',
                'data' => [
                    'id' => $lineup->id,
                    'status' => $lineup->status,
                    'status_url' => route($this->routePrefix().'.smart-squad.status', $lineup->id),
                    'show_url' => route($this->routePrefix().'.lineups.show', $lineup->id),
                ],
            ], 202);
        }

        return redirect()->route($this->routePrefix().'.lineups.show', $lineup->id)->with('success', 'AI kadro onerisi siraya alindi.');
    }

    public function status(Request $request, int $lineupId)
    {
        $lineup = $this->lineupService->show($lineupId, $request->user());

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lineup->id,
                'status' => $lineup->status,
                'error_message' => $lineup->error_message,
                'show_url' => route($this->routePrefix().'.lineups.show', $lineup->id),
            ],
        ]);
    }
}
