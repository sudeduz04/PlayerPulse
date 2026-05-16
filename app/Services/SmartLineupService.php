<?php

namespace App\Services;

use App\Models\Lineups;
use App\Models\Matches;
use App\Models\Players;
use App\Models\Positions;
use App\Models\User;
use App\Services\Ai\AiProvider;
use App\Services\Authorization\TeamAccess;
use RuntimeException;

class SmartLineupService
{
    public function __construct(
        private AiProvider $ai,
        private LineupService $lineupService,
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

    public function suggestAndStore(int $matchId, string $formation, User $user, ?string $note = null): Lineups
    {
        if (! $this->ai->isConfigured()) {
            throw new RuntimeException('AI sağlayıcısı yapılandırılmamış.');
        }

        $match = Matches::with('team')->findOrFail($matchId);
        $this->teamAccess->assertTeam($user, $match->team_id);

        $roster = Players::with(['position', 'matchStats', 'trainingPerformances', 'developmentReports' => fn ($q) => $q->latest('report_date')->limit(2)])
            ->where('team_id', $match->team_id)
            ->whereNull('deleted_at')
            ->get();

        if ($roster->count() < 11) {
            throw new RuntimeException('Takımda 11 oyuncudan az kayıt var. Önce kadroyu doldur.');
        }

        $positions = Positions::all()->keyBy('id');
        $positionsByCode = Positions::all()->keyBy('code');

        $rosterSummary = $roster->map(function (Players $p) {
            $matchAvg = $p->matchStats->isEmpty() ? null : round($p->matchStats->avg('match_rating') ?? 0, 2);
            $trainAvg = $p->trainingPerformances->isEmpty() ? null : round($p->trainingPerformances->avg('performance_score') ?? 0, 2);
            $latestReport = $p->developmentReports->first();

            return [
                'id' => $p->id,
                'name' => trim($p->first_name.' '.$p->last_name),
                'jersey' => $p->jersey_number,
                'position_code' => $p->position?->code,
                'position' => $p->position?->name,
                'avg_match_rating' => $matchAvg,
                'avg_training_score' => $trainAvg,
                'latest_overall_score' => $latestReport?->overall_score,
                'matches_played' => $p->matchStats->count(),
            ];
        })->values()->all();

        $availablePositions = $positions->map(fn ($pos) => ['id' => $pos->id, 'code' => $pos->code, 'name' => $pos->name])->values()->all();

        $prompt = "Maç bilgileri:\n".
            "Takım: {$match->team?->name}\n".
            "Rakip: {$match->opponent_team}\n".
            "Tarih: ".($match->match_date?->format('d.m.Y') ?? '-')."\n".
            "Diziliş: {$formation}\n\n".
            "Kullanılabilir oyuncular (JSON):\n".json_encode($rosterSummary, JSON_UNESCAPED_UNICODE)."\n\n".
            "Kullanılabilir pozisyonlar (JSON):\n".json_encode($availablePositions, JSON_UNESCAPED_UNICODE)."\n\n".
            "Görev: {$formation} dizilişine uygun en iyi 11 oyuncuyu seç. Her oyuncu için yukarıdaki position id'lerden birini ata. ".
            "Sadece şu formatta JSON döndür (başka açıklama ekleme): ".
            '{"players":[{"player_id":1,"position_id":2,"recommendation_score":8.5,"reason":"..."}, ...11 adet], "note":"genel kadro notu"}';

        $response = $this->ai->generateJson($prompt, [
            'system' => 'Sen profesyonel bir futbol teknik direktörüsün. Verilen oyuncu verilerine göre en iyi 11i seç. Sadece geçerli JSON döndür.',
        ]);

        if (empty($response['players']) || ! is_array($response['players']) || count($response['players']) !== 11) {
            throw new RuntimeException('AI 11 oyuncu önermedi. Yanıt: '.json_encode($response));
        }

        $rosterIds = collect($rosterSummary)->pluck('id')->all();
        $players = [];

        foreach ($response['players'] as $entry) {
            $playerId = $entry['player_id'] ?? null;
            $positionId = $entry['position_id'] ?? null;

            if (! in_array($playerId, $rosterIds, true)) {
                throw new RuntimeException("AI geçersiz oyuncu önerdi (id={$playerId}).");
            }

            if (! $positions->has($positionId)) {
                throw new RuntimeException("AI geçersiz pozisyon önerdi (id={$positionId}).");
            }

            $players[] = [
                'player_id' => $playerId,
                'position_id' => $positionId,
                'is_starting' => true,
                'recommendation_score' => isset($entry['recommendation_score']) ? (float) $entry['recommendation_score'] : null,
            ];
        }

        $aiNote = trim(($note ? $note."\n\n" : '')."AI notu: ".($response['note'] ?? '-'));

        return $this->lineupService->create([
            'match_id' => $matchId,
            'formation' => $formation,
            'note' => $aiNote,
            'players' => $players,
        ], $user, isAiGenerated: true);
    }
}
