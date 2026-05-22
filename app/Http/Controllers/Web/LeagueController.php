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
            $result = $this->fixtureService->importFile($id, $request->file('fixture_file'));
        } else {
            $rows = collect($request->validated('rows', []))
                ->filter(fn ($row) => array_filter($row))
                ->values()
                ->all();
            $result = $this->fixtureService->importManual($id, $rows);
        }

        return redirect()
            ->route('super_admin.leagues.show', $id)
            ->with('success', $result['created'].' fikstur satiri islendi.')
            ->with('fixture_skipped', $result['skipped']);
    }
}
