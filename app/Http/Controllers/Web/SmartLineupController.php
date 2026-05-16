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
        $matches = $this->lineupService->availableMatches($request->user());

        return view('smart_lineups.create', [
            'matches' => $matches,
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
            $lineup = $this->smartLineup->suggestAndStore(
                (int) $data['match_id'],
                $data['formation'],
                $request->user(),
                $data['note'] ?? null,
            );
        } catch (Throwable $e) {
            return redirect()
                ->route($this->routePrefix().'.smart-squad.create')
                ->with('error', 'AI önerisi alınamadı: '.$e->getMessage());
        }

        return redirect()
            ->route($this->routePrefix().'.lineups.show', $lineup->id)
            ->with('success', 'AI kadro önerisi oluşturuldu.');
    }
}
