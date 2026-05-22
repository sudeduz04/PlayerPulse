<?php

namespace App\Services;

use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Teams;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

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
        return Leagues::with(['teams', 'matches.homeTeam', 'matches.awayTeam'])
            ->withCount(['teams', 'matches'])
            ->findOrFail($id);
    }

    public function importFile(int $leagueId, UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = match ($extension) {
            'csv' => $this->readCsv($file),
            'xlsx', 'xls' => $this->readSpreadsheet($file),
            default => throw new RuntimeException('Yalnizca CSV, XLS veya XLSX dosyasi yuklenebilir.'),
        };

        return $this->importRows($leagueId, $rows, 'file');
    }

    public function importManual(int $leagueId, array $rows): array
    {
        return $this->importRows($leagueId, $rows, 'manual');
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
                ]
            );

            $created++;
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function readCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            throw new RuntimeException('CSV dosyasi okunamadi.');
        }

        $rows = [];
        $headers = null;

        $delimiter = ',';
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

    private function readSpreadsheet(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
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
