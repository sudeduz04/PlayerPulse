<?php

namespace App\Http\Controllers\Api;

use App\Models\FixtureImports;
use App\Services\FixtureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixtureImportController extends BaseController
{
    public function __construct(private FixtureService $fixtureService) {}

    public function store(Request $request, int $leagueId): JsonResponse
    {
        if ($request->hasFile('fixture_file')) {
            $request->validate([
                'fixture_file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:5120'],
            ]);

            $import = $this->fixtureService->queueFileImport(
                $leagueId,
                $request->file('fixture_file'),
                $request->user()
            );

            return $this->sendResponse([
                'fixture_import_id' => $import->id,
                'status' => $import->status,
                'status_url' => route('api.fixture-imports.show', $import->id),
            ], 'Fixture file queued for import.', 202);
        }

        $data = $request->validate([
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.week' => ['required'],
            'rows.*.date' => ['required'],
            'rows.*.home_team' => ['required', 'string'],
            'rows.*.away_team' => ['required', 'string'],
            'rows.*.location' => ['nullable', 'string'],
            'rows.*.status' => ['nullable', 'string'],
        ]);

        $import = $this->fixtureService->importManual($leagueId, $data['rows'], $request->user());

        return $this->sendResponse([
            'fixture_import_id' => $import->id,
            'status' => $import->status,
            'created_rows' => $import->created_rows,
            'skipped_rows' => $import->skipped_rows,
            'skipped' => $import->skipped_payload,
        ], 'Manual fixture rows imported.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $import = FixtureImports::with('league')->findOrFail($id);

        return $this->sendResponse([
            'id' => $import->id,
            'league_id' => $import->league_id,
            'status' => $import->status,
            'status_label' => \App\Support\StatusLabels::fixtureImport($import->status),
            'source' => $import->source,
            'original_filename' => $import->original_filename,
            'created_rows' => $import->created_rows,
            'skipped_rows' => $import->skipped_rows,
            'skipped' => $import->skipped_payload,
            'error_message' => $import->error_message,
            'created_at' => $import->created_at,
        ], 'Fixture import retrieved successfully.');
    }
}
