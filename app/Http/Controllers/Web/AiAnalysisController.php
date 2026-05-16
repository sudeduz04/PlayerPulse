<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Services\AiAnalysisService;
use Illuminate\Http\Request;
use Throwable;

class AiAnalysisController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private AiAnalysisService $service) {}

    public function index(Request $request)
    {
        $analyses = $this->service->list($request->query(), $request->user());

        return view('analysis.index', [
            'analyses' => $analyses,
            'players' => $this->service->availablePlayers($request->user()),
            'filters' => $request->query(),
            'aiReady' => $this->service->isAiReady(),
            'aiProvider' => $this->service->aiProviderName(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        return view('analysis.create', [
            'players' => $this->service->availablePlayers($request->user()),
            'aiReady' => $this->service->isAiReady(),
            'aiProvider' => $this->service->aiProviderName(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'player_id' => ['required', 'integer', 'exists:players,id'],
            'focus' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $analysis = $this->service->analyzePlayer(
                (int) $data['player_id'],
                $request->user(),
                $data['focus'] ?? null,
            );
        } catch (Throwable $e) {
            return redirect()
                ->route($this->routePrefix().'.analysis.create')
                ->with('error', 'AI analizi başarısız: '.$e->getMessage());
        }

        return redirect()
            ->route($this->routePrefix().'.analysis.show', $analysis->id)
            ->with('success', 'AI analizi tamamlandı.');
    }

    public function show(Request $request, int $id)
    {
        $analysis = $this->service->show($id, $request->user());

        return view('analysis.show', [
            'analysis' => $analysis,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->service->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.analysis.index')
            ->with('success', 'Analiz silindi.');
    }
}
