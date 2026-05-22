<?php

namespace Database\Seeders;

use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Teams;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeagueFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $league = Leagues::updateOrCreate(
            ['name' => 'Süper Lig', 'season' => '2025-2026'],
            ['description' => 'Trendyol Süper Lig 2025-2026 sezonu (örnek fikstür).']
        );

        $teams = Teams::where('season', '2025-2026')
            ->orderBy('id')
            ->limit(18)
            ->get();

        if ($teams->count() < 18) {
            $this->command?->warn('Süper Lig fikstürü için 18 takım gerekli, '.$teams->count().' bulundu. Önce TeamSeeder çalıştır.');
            return;
        }

        $league->teams()->sync($teams->pluck('id')->all());

        // Önceki örnek fikstürü temizle (idempotent çalışsın)
        Matches::where('league_id', $league->id)->delete();

        $pairings = $this->roundRobin($teams->pluck('id')->all());

        $startDate = Carbon::parse('2025-08-08'); // Cuma
        $now = now();
        $rows = [];

        // Tek devre: 19 hafta
        foreach ($pairings as $weekIndex => $week) {
            $weekNumber = $weekIndex + 1;
            $matchDate = $startDate->copy()->addWeeks($weekIndex)->toDateString();

            foreach ($week as $matchIndex => [$homeId, $awayId]) {
                $rows[] = $this->buildRow($league->id, $weekNumber, $matchDate, $homeId, $awayId, $matchIndex, $teams, $now);
            }
        }

        // Çift devre: 20-38. haftalar, ev/deplasman ters
        foreach ($pairings as $weekIndex => $week) {
            $weekNumber = $weekIndex + 1 + count($pairings);
            $matchDate = $startDate->copy()->addWeeks($weekNumber - 1)->toDateString();

            foreach ($week as $matchIndex => [$homeId, $awayId]) {
                $rows[] = $this->buildRow($league->id, $weekNumber, $matchDate, $awayId, $homeId, $matchIndex, $teams, $now);
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('matches')->insert($chunk);
        }

        $this->command?->info('Süper Lig 2025-2026 fikstürü: '.count($rows).' maç oluşturuldu.');
    }

    /**
     * Standard round-robin (circle method). Returns array of weeks, each week is array of [homeId, awayId] pairs.
     */
    private function roundRobin(array $ids): array
    {
        $count = count($ids);
        if ($count % 2 !== 0) {
            $ids[] = 0; // bye
            $count++;
        }

        $half = (int) ($count / 2);
        $rotating = array_slice($ids, 1); // fixed: $ids[0]
        $fixed = $ids[0];
        $weeks = [];

        for ($week = 0; $week < $count - 1; $week++) {
            $line = array_merge([$fixed], $rotating);
            $home = array_slice($line, 0, $half);
            $away = array_reverse(array_slice($line, $half));

            $pairs = [];
            for ($i = 0; $i < $half; $i++) {
                if ($home[$i] === 0 || $away[$i] === 0) {
                    continue;
                }
                // Alternate home/away by week to keep balance
                if ($week % 2 === 0) {
                    $pairs[] = [$home[$i], $away[$i]];
                } else {
                    $pairs[] = [$away[$i], $home[$i]];
                }
            }
            $weeks[] = $pairs;

            // Rotate
            $rotating = array_merge([array_pop($rotating)], $rotating);
        }

        return $weeks;
    }

    private function buildRow(int $leagueId, int $week, string $date, int $homeId, int $awayId, int $matchIndex, $teams, $now): array
    {
        $homeName = $teams->firstWhere('id', $homeId)?->name ?? '-';
        $awayName = $teams->firstWhere('id', $awayId)?->name ?? '-';

        // İlk 14 hafta finished
        if ($week <= 14) {
            $goalsFor = random_int(0, 4);
            $goalsAgainst = random_int(0, 4);
            $result = $goalsFor > $goalsAgainst ? 'W' : ($goalsFor < $goalsAgainst ? 'L' : 'D');
            $status = Matches::STATUS_FINISHED;
        } elseif ($week === 15 && $matchIndex < 2) {
            // Hafta 15'te ilk 2 maç canlı (first_half)
            $goalsFor = random_int(0, 2);
            $goalsAgainst = random_int(0, 2);
            $result = null;
            $status = Matches::STATUS_FIRST_HALF;
        } else {
            $goalsFor = 0;
            $goalsAgainst = 0;
            $result = null;
            $status = Matches::STATUS_SCHEDULED;
        }

        return [
            'league_id' => $leagueId,
            'week' => $week,
            'team_id' => $homeId,
            'home_team_id' => $homeId,
            'away_team_id' => $awayId,
            'opponent_team' => $awayName,
            'match_date' => $date,
            'kickoff_time' => $matchIndex < 2 ? '19:00:00' : '16:00:00',
            'match_type' => 'league',
            'fixture_source' => 'seeder',
            'location' => $this->stadium($homeName),
            'result' => $result,
            'status' => $status,
            'goals_for' => $goalsFor,
            'goals_against' => $goalsAgainst,
            'coach_note' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function stadium(string $teamName): string
    {
        return match ($teamName) {
            'Galatasaray' => 'Rams Park',
            'Fenerbahçe' => 'Şükrü Saracoğlu',
            'Beşiktaş' => 'Tüpraş Stadyumu',
            'Trabzonspor' => 'Papara Park',
            'Başakşehir' => 'Başakşehir Fatih Terim Stadyumu',
            'Adana Demirspor' => '5 Ocak Stadyumu',
            'Antalyaspor' => 'Antalya Stadyumu',
            'Alanyaspor' => 'Alanya Oba Stadyumu',
            'Konyaspor' => 'Konya Büyükşehir Stadyumu',
            'Sivasspor' => 'Yeni 4 Eylül Stadyumu',
            'Kasımpaşa' => 'Recep Tayyip Erdoğan Stadyumu',
            'Samsunspor' => 'Samsun 19 Mayıs Stadyumu',
            'Kayserispor' => 'Kayseri Şehir Stadyumu',
            'Gaziantep FK' => 'Gaziantep Stadyumu',
            'Hatayspor' => 'Mersin Stadyumu',
            'Rizespor' => 'Çaykur Didi Stadı',
            'Pendikspor' => 'Pendik Stadyumu',
            'İstanbulspor' => 'Necmi Kadıoğlu Stadı',
            'Bodrum FK' => 'Bodrum İlçe Stadyumu',
            'Eyüpspor' => 'Eyüp Stadyumu',
            default => $teamName.' Stadı',
        };
    }
}
