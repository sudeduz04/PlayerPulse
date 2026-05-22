<?php

namespace App\Services;

use App\Jobs\ImportFixtureFileJob;
use App\Models\FixtureImports;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Teams;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;
use Throwable;

class FixtureService
{
    public function list(array $filters)
    {
        $query = Leagues::withCount(['teams', 'matches'])->with('teams');

        if (! empty($filters['season'])) {
            $query->where('season', $filters['season']);
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate(15);
    }

    public function createLeague(array $data): Leagues
    {
        $league = Leagues::create([
            'name' => $data['name'],
            'season' => $data['season'],
            'description' => $data['description'] ?? null,
        ]);

        $league->teams()->sync($data['team_ids'] ?? []);

        return $league->load('teams');
    }

    public function updateLeague(int $id, array $data): Leagues
    {
        $league = Leagues::findOrFail($id);
        $league->update([
            'name' => $data['name'],
            'season' => $data['season'],
            'description' => $data['description'] ?? null,
        ]);
        $league->teams()->sync($data['team_ids'] ?? []);

        return $league->fresh('teams');
    }

    public function deleteLeague(int $id): void
    {
        Leagues::findOrFail($id)->delete();
    }

    public function show(int $id): Leagues
    {
        return Leagues::with([
            'teams',
            'matches.homeTeam',
            'matches.awayTeam',
            'fixtureImports' => fn ($q) => $q->latest('id')->limit(10),
            'fixtureImports.user',
        ])
            ->withCount(['teams', 'matches'])
            ->findOrFail($id);
    }

    /**
     * Queue a file-based fixture import. File is stored, a tracking row is created,
     * and a job is dispatched to parse it asynchronously.
     */
    public function queueFileImport(int $leagueId, UploadedFile $file, ?User $user = null): FixtureImports
    {
        $league = Leagues::findOrFail($leagueId);
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
            throw new RuntimeException('Yalnizca CSV, XLS veya XLSX dosyasi yuklenebilir.');
        }

        $filename = sprintf('league-%d-%s.%s', $league->id, uniqid('', true), $extension);
        $path = Storage::putFileAs('fixtures/imports', $file, $filename);

        $import = FixtureImports::create([
            'league_id' => $league->id,
            'user_id' => $user?->id,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'source' => 'file',
            'status' => FixtureImports::STATUS_QUEUED,
        ]);

        ImportFixtureFileJob::dispatch($import->id);

        return $import;
    }

    /**
     * Synchronous file import — kept for tests and callers that need the result immediately.
     * Returns ['created' => int, 'skipped' => array].
     */
    public function importFile(int $leagueId, UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = match ($extension) {
            'csv' => $this->readCsvFromPath($file->getRealPath()),
            'xlsx', 'xls' => $this->readSpreadsheetFromPath($file->getRealPath()),
            default => throw new RuntimeException('Yalnizca CSV, XLS veya XLSX dosyasi yuklenebilir.'),
        };

        return $this->importRows($leagueId, $rows, 'file');
    }

    public function importManual(int $leagueId, array $rows, ?User $user = null): FixtureImports
    {
        $import = FixtureImports::create([
            'league_id' => $leagueId,
            'user_id' => $user?->id,
            'source' => 'manual',
            'status' => FixtureImports::STATUS_RUNNING,
        ]);

        try {
            $result = $this->importRows($leagueId, $rows, 'manual');
            $import->update([
                'status' => FixtureImports::STATUS_COMPLETED,
                'created_rows' => $result['created'],
                'skipped_rows' => count($result['skipped']),
                'skipped_payload' => $result['skipped'],
            ]);
        } catch (Throwable $e) {
            $import->update([
                'status' => FixtureImports::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $import->fresh();
    }

    /**
     * Read a previously stored file and import it. Called by ImportFixtureFileJob.
     */
    public function processStoredFile(int $fixtureImportId): FixtureImports
    {
        $import = FixtureImports::findOrFail($fixtureImportId);
        $import->update(['status' => FixtureImports::STATUS_RUNNING, 'error_message' => null]);

        try {
            $path = $import->file_path ?: '';
            if (! $path || ! Storage::exists($path)) {
                throw new RuntimeException('Yuklenen dosya bulunamadi.');
            }

            $absolute = Storage::path($path);
            $extension = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));

            $rows = match ($extension) {
                'csv' => $this->readCsvFromPath($absolute),
                'xlsx', 'xls' => $this->readSpreadsheetFromPath($absolute),
                default => throw new RuntimeException('Yalnizca CSV, XLS veya XLSX dosyasi yuklenebilir.'),
            };

            $result = $this->importRows($import->league_id, $rows, 'file');

            $import->update([
                'status' => FixtureImports::STATUS_COMPLETED,
                'created_rows' => $result['created'],
                'skipped_rows' => count($result['skipped']),
                'skipped_payload' => $result['skipped'],
            ]);
        } catch (Throwable $e) {
            $import->update([
                'status' => FixtureImports::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $import->fresh();
    }

    private function importRows(int $leagueId, array $rows, string $source): array
    {
        $league = Leagues::with('teams')->findOrFail($leagueId);
        $teamMap = $league->teams->keyBy(fn (Teams $team) => mb_strtolower(trim($team->name)));
        $created = 0;
        $skipped = [];

        foreach ($rows as $index => $row) {
            $normalized = $this->normalizeRow($row);

            if (! $normalized['week'] || ! $normalized['date'] || ! $normalized['home_team'] || ! $normalized['away_team']) {
                $skipped[] = ['row' => $index + 1, 'reason' => 'Eksik hafta, tarih, ev sahibi veya deplasman bilgisi.'];

                continue;
            }

            $home = $teamMap->get(mb_strtolower($normalized['home_team']));
            $away = $teamMap->get(mb_strtolower($normalized['away_team']));

            if (! $home || ! $away) {
                $skipped[] = ['row' => $index + 1, 'reason' => 'Takim adi lig takimlariyla eslesmedi.'];

                continue;
            }

            if ($home->id === $away->id) {
                $skipped[] = ['row' => $index + 1, 'reason' => 'Ev sahibi ve deplasman ayni olamaz.'];

                continue;
            }

            try {
                $matchDate = $this->date($normalized['date']);
            } catch (\Throwable) {
                $skipped[] = ['row' => $index + 1, 'reason' => 'Tarih formati okunamadi.'];

                continue;
            }

            $status = $normalized['status'] ?: Matches::STATUS_SCHEDULED;
            if (! in_array($status, Matches::STATUSES, true)) {
                $status = Matches::STATUS_SCHEDULED;
            }

            Matches::updateOrCreate(
                [
                    'league_id' => $league->id,
                    'week' => (int) $normalized['week'],
                    'home_team_id' => $home->id,
                    'away_team_id' => $away->id,
                ],
                [
                    'team_id' => $home->id,
                    'opponent_team' => $away->name,
                    'match_date' => $matchDate,
                    'match_type' => 'league',
                    'fixture_source' => $source,
                    'location' => $normalized['location'] ?: 'Lig fiksturu',
                    'status' => $status,
                ]
            );

            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function readCsvFromPath(string $path): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new RuntimeException('CSV dosyasi okunamadi.');
        }

        $rows = [];
        $headers = null;

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return [];
        }
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($headers === null) {
                $headers = array_map(fn ($value) => $this->key($value), $line);

                continue;
            }

            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        fclose($handle);

        return $rows;
    }

    private function readSpreadsheetFromPath(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, true);
        $headers = [];
        $rows = [];

        foreach ($rawRows as $rowIndex => $row) {
            $values = array_values($row);

            if ($rowIndex === 1) {
                $headers = array_map(fn ($value) => $this->key($value), $values);

                continue;
            }

            if (count(array_filter($values, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }

            $rows[] = array_combine($headers, array_pad($values, count($headers), null));
        }

        return $rows;
    }

    private function normalizeRow(array $row): array
    {
        return [
            'week' => $row['week'] ?? $row['hafta'] ?? null,
            'date' => $row['date'] ?? $row['tarih'] ?? null,
            'home_team' => trim((string) ($row['home_team'] ?? $row['ev_sahibi'] ?? $row['evsahibi'] ?? '')),
            'away_team' => trim((string) ($row['away_team'] ?? $row['deplasman'] ?? $row['misafir'] ?? '')),
            'location' => trim((string) ($row['location'] ?? $row['saha'] ?? $row['lokasyon'] ?? '')),
            'status' => trim((string) ($row['status'] ?? $row['durum'] ?? '')) ?: null,
        ];
    }

    private function key(mixed $value): string
    {
        return str_replace(["\xEF\xBB\xBF", ' ', '-'], ['', '_', '_'], mb_strtolower(trim((string) $value)));
    }

    private function date(mixed $value): string
    {
        if (is_numeric($value)) {
            return Carbon::createFromDate(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        $text = trim((string) $value);

        foreach (['Y-m-d', 'd.m.Y', 'd/m/Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $text)->toDateString();
            } catch (\Throwable) {
                //
            }
        }

        return Carbon::parse($text)->toDateString();
    }
}
