<?php

namespace App\Services;

use App\Jobs\GenerateAiAnalysisJob;
use App\Models\AiRecommendations;
use App\Models\Players;
use App\Models\User;
use App\Services\Ai\AiProvider;
use App\Services\Authorization\TeamAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RuntimeException;
use Throwable;

class AiAnalysisService
{
    public function __construct(
        private AiProvider $ai,
        private TeamAccess $teamAccess,
    ) {}

    public function isAiReady(): bool
    {
        return $this->ai->isConfigured();
    }

    public function aiProviderName(): string
    {
        return $this->ai->name();
    }

    public function list(array $filters, User $user): LengthAwarePaginator
    {
        $query = AiRecommendations::with(['player.team']);

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $teamIds = $user->getTeamIds()->all();
            $query->whereHas('player', fn ($q) => $q->whereIn('team_id', $teamIds));
        }

        if (! empty($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (! empty($filters['type'])) {
            $query->where('recommendation_type', $filters['type']);
        }

        return $query->latest('id')->paginate(15);
    }

    public function show(int $id, User $user): AiRecommendations
    {
        $analysis = AiRecommendations::with(['player.team'])->findOrFail($id);

        $this->teamAccess->assertPlayer($user, $analysis->player);

        return $analysis;
    }

    public function availablePlayers(User $user): Collection
    {
        $query = Players::with(['team', 'position'])->whereNull('deleted_at');

        if ($user->isRole(User::ROLE_COACH) || $user->isRole(User::ROLE_MANAGER)) {
            $query->whereIn('team_id', $user->getTeamIds());
        }

        return $query->orderBy('first_name')->get();
    }

    public function analyzePlayer(int $playerId, User $user, ?string $focus = null): AiRecommendations
    {
        if (! $this->ai->isConfigured()) {
            throw new RuntimeException('AI sağlayıcısı yapılandırılmamış.');
        }

        $player = Players::with([
            'team',
            'position',
            'matchStats.match',
            'trainingPerformances.training',
            'developmentReports' => fn ($q) => $q->latest('report_date')->limit(3),
            'injuries' => fn ($q) => $q->latest('start_date')->limit(3),
            'physicalMeasurements' => fn ($q) => $q->latest('measurement_date')->limit(3),
        ])->findOrFail($playerId);

        $this->teamAccess->assertPlayer($user, $player);

        $context = $this->buildPlayerContext($player);

        $prompt = "Oyuncu verileri (JSON):\n".json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n\n".
            ($focus ? "Özel odak: {$focus}\n\n" : '').
            'Görev: Bu oyuncuyu profesyonel bir teknik direktör gözüyle analiz et. '.
            'Sadece şu formatta JSON döndür: '.
            '{"overall_score": 0.0-10.0, "summary": "kısa özet 1-2 cümle", "strengths": "güçlü yönler markdown", "weaknesses": "gelişim alanları markdown", "recommendations": "öneriler markdown"}';

        $response = $this->ai->generateJson($prompt, [
            'system' => 'Sen profesyonel bir futbol analistisin. Türkçe analiz yap. Sadece geçerli JSON döndür.',
        ]);

        $score = isset($response['overall_score']) ? (float) $response['overall_score'] : null;
        if ($score !== null) {
            $score = max(0.0, min(10.0, $score));
        }

        $reasonText = trim(
            "## Özet\n".($response['summary'] ?? '-')."\n\n".
            "## Güçlü Yönler\n".($response['strengths'] ?? '-')."\n\n".
            "## Gelişim Alanları\n".($response['weaknesses'] ?? '-')."\n\n".
            "## Öneriler\n".($response['recommendations'] ?? '-')
        );

        return AiRecommendations::create([
            'player_id' => $player->id,
            'match_id' => null,
            'recommendation_type' => 'player_analysis',
            'status' => 'completed',
            'score' => $score,
            'reason' => $reasonText,
        ]);
    }

    public function queuePlayerAnalysis(int $playerId, User $user, ?string $focus = null): AiRecommendations
    {
        if (! $this->ai->isConfigured()) {
            throw new RuntimeException('AI saglayicisi yapilandirilmamis.');
        }

        $player = Players::findOrFail($playerId);
        $this->teamAccess->assertPlayer($user, $player);

        $analysis = AiRecommendations::create([
            'player_id' => $player->id,
            'match_id' => null,
            'recommendation_type' => 'player_analysis',
            'status' => 'queued',
            'metadata' => ['focus' => $focus, 'requested_by' => $user->id],
        ]);

        GenerateAiAnalysisJob::dispatch($analysis->id);

        return $analysis;
    }

    public function processQueuedAnalysis(int $analysisId): AiRecommendations
    {
        $analysis = AiRecommendations::findOrFail($analysisId);
        $analysis->update(['status' => 'running', 'error_message' => null]);

        try {
            $requester = User::findOrFail((int) ($analysis->metadata['requested_by'] ?? 0));
            $generated = $this->analyzePlayer((int) $analysis->player_id, $requester, $analysis->metadata['focus'] ?? null);

            $analysis->update([
                'status' => 'completed',
                'score' => $generated->score,
                'reason' => $generated->reason,
                'error_message' => null,
            ]);
            $generated->delete();

            return $analysis->fresh(['player.team']);
        } catch (Throwable $e) {
            $analysis->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function delete(int $id, User $user): void
    {
        $analysis = AiRecommendations::with('player')->findOrFail($id);

        $this->teamAccess->assertPlayer($user, $analysis->player);

        $analysis->delete();
    }

    private function buildPlayerContext(Players $player): array
    {
        $matchStats = $player->matchStats;
        $training = $player->trainingPerformances;

        return [
            'name' => trim($player->first_name.' '.$player->last_name),
            'jersey' => $player->jersey_number,
            'team' => $player->team?->name,
            'position' => $player->position?->name,
            'dominant_foot' => $player->dominant_foot,
            'birth_date' => $player->birth_date?->format('Y-m-d'),
            'height' => $player->height,
            'weight' => $player->weight,
            'matches_played' => $matchStats->count(),
            'avg_match_rating' => $matchStats->isEmpty() ? null : round($matchStats->avg('match_rating') ?? 0, 2),
            'total_goals' => $matchStats->sum('goals'),
            'total_assists' => $matchStats->sum('assists'),
            'avg_pass_accuracy' => $matchStats->isEmpty() ? null : round($matchStats->avg('pass_accuracy') ?? 0, 2),
            'trainings_attended' => $training->where('attendance_status', 'attended')->count(),
            'avg_training_score' => $training->isEmpty() ? null : round($training->avg('performance_score') ?? 0, 2),
            'avg_speed' => $training->isEmpty() ? null : round($training->avg('speed_score') ?? 0, 2),
            'avg_endurance' => $training->isEmpty() ? null : round($training->avg('endurance_score') ?? 0, 2),
            'avg_technique' => $training->isEmpty() ? null : round($training->avg('technique_score') ?? 0, 2),
            'avg_discipline' => $training->isEmpty() ? null : round($training->avg('discipline_score') ?? 0, 2),
            'recent_reports' => $player->developmentReports->map(fn ($r) => [
                'date' => $r->report_date?->format('Y-m-d'),
                'technical' => $r->technical_development,
                'physical' => $r->physical_development,
                'tactical' => $r->tactical_development,
                'mental' => $r->mental_development,
                'overall' => $r->overall_score,
                'strengths' => $r->strengths,
                'weaknesses' => $r->weaknesses,
            ])->values()->all(),
            'recent_injuries' => $player->injuries->map(fn ($i) => [
                'type' => $i->injury_type,
                'start' => $i->start_date?->format('Y-m-d'),
                'end' => $i->end_date?->format('Y-m-d'),
                'status' => $i->status,
            ])->values()->all(),
            'recent_measurements' => $player->physicalMeasurements->map(fn ($m) => [
                'date' => $m->measurement_date?->format('Y-m-d'),
                'weight' => $m->weight,
                'body_fat' => $m->body_fat_percentage,
                'sprint_time' => $m->sprint_time,
                'agility' => $m->agility_score,
                'endurance' => $m->endurance_level,
                'strength' => $m->strength_score,
            ])->values()->all(),
        ];
    }
}
