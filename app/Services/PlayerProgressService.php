<?php

namespace App\Services;

use App\Models\Players;

class PlayerProgressService
{
    public function buildChartData(Players $player): array
    {
        return [
            'development' => $this->developmentTrend($player),
            'matches' => $this->matchTrend($player),
            'trainings' => $this->trainingTrend($player),
            'measurements' => $this->measurementTrend($player),
        ];
    }

    private function developmentTrend(Players $player): array
    {
        $reports = $player->developmentReports()
            ->orderBy('report_date')
            ->get();

        if ($reports->isEmpty()) {
            return ['categories' => [], 'series' => []];
        }

        $categories = $reports->map(fn ($r) => $r->report_date->format('d.m.Y'))->all();

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Teknik', 'data' => $reports->map(fn ($r) => $this->floatOrNull($r->technical_development))->all()],
                ['name' => 'Fiziksel', 'data' => $reports->map(fn ($r) => $this->floatOrNull($r->physical_development))->all()],
                ['name' => 'Taktik', 'data' => $reports->map(fn ($r) => $this->floatOrNull($r->tactical_development))->all()],
                ['name' => 'Mental', 'data' => $reports->map(fn ($r) => $this->floatOrNull($r->mental_development))->all()],
                ['name' => 'Genel', 'data' => $reports->map(fn ($r) => $this->floatOrNull($r->overall_score))->all()],
            ],
        ];
    }

    private function matchTrend(Players $player): array
    {
        $stats = $player->matchStats()
            ->with('match')
            ->get()
            ->filter(fn ($s) => $s->match !== null)
            ->sortBy(fn ($s) => $s->match->match_date)
            ->values();

        if ($stats->isEmpty()) {
            return ['categories' => [], 'series' => []];
        }

        $categories = $stats->map(fn ($s) => $s->match->match_date->format('d.m.Y'))->all();

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Maç Puanı', 'data' => $stats->map(fn ($s) => $this->floatOrNull($s->match_rating))->all()],
                ['name' => 'Pas İsabeti %', 'data' => $stats->map(fn ($s) => $this->floatOrNull($s->pass_accuracy))->all()],
                ['name' => 'Gol', 'data' => $stats->map(fn ($s) => $this->intOrNull($s->goals))->all()],
                ['name' => 'Asist', 'data' => $stats->map(fn ($s) => $this->intOrNull($s->assists))->all()],
            ],
        ];
    }

    private function trainingTrend(Players $player): array
    {
        $perfs = $player->trainingPerformances()
            ->with('training')
            ->where('attendance_status', 'attended')
            ->get()
            ->filter(fn ($p) => $p->training !== null)
            ->sortBy(fn ($p) => $p->training->training_date)
            ->values();

        if ($perfs->isEmpty()) {
            return ['categories' => [], 'series' => []];
        }

        $categories = $perfs->map(fn ($p) => $p->training->training_date->format('d.m.Y'))->all();

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Performans', 'data' => $perfs->map(fn ($p) => $this->floatOrNull($p->performance_score))->all()],
                ['name' => 'Hız', 'data' => $perfs->map(fn ($p) => $this->floatOrNull($p->speed_score))->all()],
                ['name' => 'Dayanıklılık', 'data' => $perfs->map(fn ($p) => $this->floatOrNull($p->endurance_score))->all()],
                ['name' => 'Teknik', 'data' => $perfs->map(fn ($p) => $this->floatOrNull($p->technique_score))->all()],
                ['name' => 'Disiplin', 'data' => $perfs->map(fn ($p) => $this->floatOrNull($p->discipline_score))->all()],
            ],
        ];
    }

    private function measurementTrend(Players $player): array
    {
        $measurements = $player->physicalMeasurements()
            ->orderBy('measurement_date')
            ->get();

        if ($measurements->isEmpty()) {
            return ['categories' => [], 'series' => []];
        }

        $categories = $measurements->map(fn ($m) => $m->measurement_date->format('d.m.Y'))->all();

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Boy (cm)', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->height))->all()],
                ['name' => 'Kilo (kg)', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->weight))->all()],
                ['name' => 'Vücut Yağ %', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->body_fat_percentage))->all()],
                ['name' => 'Sprint (s, düşük iyi)', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->sprint_time))->all()],
                ['name' => 'Çeviklik', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->agility_score))->all()],
                ['name' => 'Dayanıklılık', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->endurance_level))->all()],
                ['name' => 'Güç', 'data' => $measurements->map(fn ($m) => $this->floatOrNull($m->strength_score))->all()],
            ],
        ];
    }

    private function floatOrNull($value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    private function intOrNull($value): ?int
    {
        return $value === null ? null : (int) $value;
    }
}
