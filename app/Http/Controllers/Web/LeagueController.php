<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\League\ImportFixtureRequest;
use App\Http\Requests\Web\League\StoreLeagueRequest;
use App\Models\FixtureImports;
use App\Models\Matches;
use App\Models\Teams;
use App\Services\FixtureService;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function __construct(private FixtureService $fixtureService) {}

    public function index(Request $request)
    {
        return view('leagues.index', [
            'leagues' => $this->fixtureService->list($request->query()),
            'filters' => $request->query(),
            'isReadOnly' => false,
            'routePrefix' => 'super_admin.leagues',
        ]);
    }

    public function create()
    {
        return view('leagues.create', [
            'teams' => Teams::orderBy('name')->get(),
        ]);
    }

    public function store(StoreLeagueRequest $request)
    {
        $league = $this->fixtureService->createLeague($request->validated());

        return redirect()->route('super_admin.leagues.show', $league->id)->with('success', 'Lig olusturuldu.');
    }

    public function show(Request $request, int $id)
    {
        return $this->renderShow($request, $id, 'super_admin.leagues', false);
    }

    public function edit(int $id)
    {
        return view('leagues.edit', [
            'league' => $this->fixtureService->show($id),
            'teams' => Teams::orderBy('name')->get(),
        ]);
    }

    public function update(StoreLeagueRequest $request, int $id)
    {
        $league = $this->fixtureService->updateLeague($id, $request->validated());

        return redirect()->route('super_admin.leagues.show', $league->id)->with('success', 'Lig guncellendi.');
    }

    public function destroy(int $id)
    {
        $this->fixtureService->deleteLeague($id);

        return redirect()->route('super_admin.leagues.index')->with('success', 'Lig silindi.');
    }

    public function import(ImportFixtureRequest $request, int $id)
    {
        if ($request->hasFile('fixture_file')) {
            $import = $this->fixtureService->queueFileImport($id, $request->file('fixture_file'), $request->user());

            return redirect()
                ->route('super_admin.leagues.show', $id)
                ->with('success', 'Fikstur dosyasi siraya alindi. Durum panelinden takip et.')
                ->with('fixture_import_id', $import->id);
        }

        $rows = collect($request->validated('rows', []))
            ->filter(fn ($row) => array_filter($row))
            ->values()
            ->all();

        $import = $this->fixtureService->importManual($id, $rows, $request->user());

        return redirect()
            ->route('super_admin.leagues.show', $id)
            ->with('success', $import->created_rows.' fikstur satiri islendi.')
            ->with('fixture_skipped', $import->skipped_payload ?? []);
    }

    public function importStatus(int $id, int $importId)
    {
        $import = FixtureImports::where('league_id', $id)->findOrFail($importId);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $import->id,
                'status' => $import->status,
                'status_label' => \App\Support\StatusLabels::fixtureImport($import->status),
                'created_rows' => $import->created_rows,
                'skipped_rows' => $import->skipped_rows,
                'error_message' => $import->error_message,
                'original_filename' => $import->original_filename,
            ],
        ]);
    }

    // ------- Public (tüm roller, read-only) -------

    public function publicIndex(Request $request)
    {
        return view('leagues.index', [
            'leagues' => $this->fixtureService->list($request->query()),
            'filters' => $request->query(),
            'isReadOnly' => true,
            'routePrefix' => 'fixtures',
        ]);
    }

    public function publicShow(Request $request, int $id)
    {
        return $this->renderShow($request, $id, 'fixtures', true);
    }

    // ------- Shared rendering -------

    private function renderShow(Request $request, int $id, string $routePrefix, bool $isReadOnly)
    {
        $league = $this->fixtureService->show($id);

        $matches = $league->matches->sortBy(['week', 'match_date', 'id'])->values();
        $weeks = $matches->pluck('week')->filter()->unique()->sort()->values();

        $defaultWeek = $this->resolveDefaultWeek($matches, $weeks);
        $requestedWeek = (int) $request->query('week', $defaultWeek);
        if (! $weeks->contains($requestedWeek)) {
            $requestedWeek = $defaultWeek;
        }

        $weekMatches = $matches->where('week', $requestedWeek)->values();

        return view('leagues.show', [
            'league' => $league,
            'teams' => Teams::orderBy('name')->get(),
            'weeks' => $weeks,
            'currentWeek' => $requestedWeek,
            'weekMatches' => $weekMatches,
            'previousWeek' => $weeks->filter(fn ($w) => $w < $requestedWeek)->last(),
            'nextWeek' => $weeks->filter(fn ($w) => $w > $requestedWeek)->first(),
            'finishedCount' => $matches->where('status', Matches::STATUS_FINISHED)->count(),
            'liveCount' => $matches->whereIn('status', [Matches::STATUS_FIRST_HALF, Matches::STATUS_HALF_TIME, Matches::STATUS_SECOND_HALF])->count(),
            'scheduledCount' => $matches->where('status', Matches::STATUS_SCHEDULED)->count(),
            'isReadOnly' => $isReadOnly,
            'routePrefix' => $routePrefix,
        ]);
    }

    private function resolveDefaultWeek($matches, $weeks): int
    {
        if ($weeks->isEmpty()) {
            return 0;
        }

        // İlk oynanmamış (scheduled/first_half/...) maçın haftası — yoksa son hafta
        $firstUpcoming = $matches->first(fn ($m) => $m->status !== Matches::STATUS_FINISHED);

        return $firstUpcoming?->week ?? $weeks->last();
    }
}
