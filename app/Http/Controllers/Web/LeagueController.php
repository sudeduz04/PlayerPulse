<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\League\ImportFixtureRequest;
use App\Http\Requests\Web\League\StoreLeagueRequest;
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

    public function show(int $id)
    {
        return view('leagues.show', [
            'league' => $this->fixtureService->show($id),
            'teams' => Teams::orderBy('name')->get(),
        ]);
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
        $import = \App\Models\FixtureImports::where('league_id', $id)->findOrFail($importId);

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
}
