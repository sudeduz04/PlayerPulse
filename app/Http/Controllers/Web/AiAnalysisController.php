<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Services\AiAnalysisService;
use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;
use Throwable;

class AiAnalysisController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private AiAnalysisService $service) {}

    public function index(Request $request)
    {
        return view('analysis.index', [
            'analyses' => $this->service->list($request->query(), $request->user()),
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
            $analysis = $this->service->queuePlayerAnalysis((int) $data['player_id'], $request->user(), $data['focus'] ?? null);
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'AI analizi baslatilamadi: '.$e->getMessage()], 400);
            }

            return redirect()->route($this->routePrefix().'.analysis.create')->with('error', 'AI analizi baslatilamadi: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Analiz siraya alindi.',
                'data' => [
                    'id' => $analysis->id,
                    'status' => $analysis->status,
                    'status_url' => route($this->routePrefix().'.analysis.status', $analysis->id),
                    'show_url' => route($this->routePrefix().'.analysis.show', $analysis->id),
                ],
            ], 202);
        }

        return redirect()->route($this->routePrefix().'.analysis.show', $analysis->id)->with('success', 'AI analizi siraya alindi.');
    }

    public function show(Request $request, int $id)
    {
        $analysis = $this->service->show($id, $request->user());

        return view('analysis.show', [
            'analysis' => $analysis,
            'analysisHtml' => $this->markdown($analysis->reason ?? ''),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function status(Request $request, int $id)
    {
        $analysis = $this->service->show($id, $request->user());

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $analysis->id,
                'status' => $analysis->status,
                'score' => $analysis->score,
                'error_message' => $analysis->error_message,
                'reason_html' => $analysis->reason ? $this->markdown($analysis->reason) : null,
                'show_url' => route($this->routePrefix().'.analysis.show', $analysis->id),
            ],
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->service->delete($id, $request->user());

        return redirect()->route($this->routePrefix().'.analysis.index')->with('success', 'Analiz silindi.');
    }

    private function markdown(string $text): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return (string) $converter->convert($text);
    }
}
