<?php

namespace App\Services;

use App\Jobs\GenerateSmartLineupJob;
use App\Models\Lineups;
use App\Models\Players;
use App\Models\Positions;
use App\Models\User;
use App\Services\Ai\AiProvider;
use App\Services\Authorization\TeamAccess;
use RuntimeException;
use Throwable;

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

    public function queueSuggestion(int $matchId, string $formation, User $user, ?string $note = null): Lineups
    {
        if (! $this->ai->isConfigured()) {
            throw new RuntimeException('AI saglayicisi yapilandirilmamis.');
        }

        $lineup = $this->lineupService->createQueued([
            'match_id' => $matchId,
            'formation' => $formation,
            'note' => $note,
        ], $user);

        GenerateSmartLineupJob::dispatch($lineup->id);

        return $lineup;
    }

    public function suggestAndStore(int $matchId, string $formation, User $user, ?string $note = null): Lineups
    {
        $lineup = $this->lineupService->createQueued([
            'match_id' => $matchId,
            'formation' => $formation,
            'note' => $note,
        ], $user);

        return $this->processQueuedLineup($lineup->id);
    }

    public function processQueuedLineup(int $lineupId): Lineups
    {
        $lineup = Lineups::with(['match.team', 'match.homeTeam', 'match.awayTeam', 'creator'])->findOrFail($lineupId);
        $lineup->update(['status' => 'running', 'error_message' => null]);

        try {
            $players = $this->buildSuggestion($lineup);
            $aiNote = trim(($lineup->note ? $lineup->note."\n\n" : '').'AI notu: '.($players['note'] ?? '-'));

            return $this->lineupService->completeQueued($lineup, $players['players'], $aiNote);
        } catch (Throwable $e) {
            $this->lineupService->failQueued($lineup, $e->getMessage());
            throw $e;
        }
    }

    private function buildSuggestion(Lineups $lineup): array
    {
        if (! $this->ai->isConfigured()) {
            throw new RuntimeException('AI saglayicisi yapilandirilmamis.');
        }

        $match = $lineup->match;
        $this->teamAccess->assertMatch($lineup->creator, $match);

        $teamId = $this->lineupService->resolveUserTeamId($match, $lineup->creator);
        $ownTeam = \App\Models\Teams::find($teamId);
        $opponentTeamName = $this->resolveOpponentName($match, $teamId);
        $isHome = $match->home_team_id === $teamId || (! $match->home_team_id && $match->team_id === $teamId);

        $roster = Players::with(['position', 'matchStats', 'trainingPerformances', 'developmentReports' => fn ($q) => $q->latest('report_date')->limit(2)])
            ->where('team_id', $teamId)
            ->whereNull('deleted_at')
            ->get();

        if ($roster->count() < 11) {
            throw new RuntimeException('Takimda 11 oyuncudan az kayit var. Once kadroyu doldur.');
        }

        $positions = Positions::all()->keyBy('id');
        $slots = $this->lineupService->formationSlots($lineup->formation);

        $rosterSummary = $roster->map(function (Players $p) {
            return [
                'id' => $p->id,
                'name' => trim($p->first_name.' '.$p->last_name),
                'jersey' => $p->jersey_number,
                'position_code' => $p->position?->code,
                'position' => $p->position?->name,
                'avg_match_rating' => $p->matchStats->isEmpty() ? null : round($p->matchStats->avg('match_rating') ?? 0, 2),
                'avg_training_score' => $p->trainingPerformances->isEmpty() ? null : round($p->trainingPerformances->avg('performance_score') ?? 0, 2),
                'latest_overall_score' => $p->developmentReports->first()?->overall_score,
                'matches_played' => $p->matchStats->count(),
            ];
        })->values()->all();

        $prompt = "Mac bilgileri:\n".
            'Bizim takim: '.($ownTeam?->name ?? '-').' ('.($isHome ? 'ev sahibi' : 'deplasman').")\n".
            'Rakip takim: '.$opponentTeamName."\n".
            'Tarih: '.($match->match_date?->format('d.m.Y') ?? '-')."\n".
            "Dizilis: {$lineup->formation}\n\n".
            "Onemli: Asagidaki oyuncular yalnizca bizim takimimizin (".($ownTeam?->name ?? '').") kadrosudur. Rakip takim oyuncusu secme.\n\n".
            "Dizilis slotlari (JSON, TAMAMI doldurulacak):\n".json_encode($slots, JSON_UNESCAPED_UNICODE)."\n\n".
            "Kullanilabilir oyuncular (JSON):\n".json_encode($rosterSummary, JSON_UNESCAPED_UNICODE)."\n\n".
            "Kullanilabilir pozisyonlar (JSON):\n".json_encode($positions->values()->map(fn ($pos) => ['id' => $pos->id, 'code' => $pos->code, 'name' => $pos->name]), JSON_UNESCAPED_UNICODE)."\n\n".
            'Gorev: Slot listesindeki HER slot icin farkli bir oyuncu sec. Cikti tam 11 oyuncu olmak zorunda. '.
            'Sadece su JSON formati: {"players":[{"slot_key":"GK","player_id":1,"position_id":2,"recommendation_score":8.5,"reason":"..."}], "note":"genel not"}';

        $response = $this->ai->generateJson($prompt, [
            'system' => 'Sen profesyonel bir futbol teknik direktorusun. Tam 11 farkli oyuncu sec. Sadece gecerli JSON dondur.',
            'temperature' => 0.2,
        ]);

        $players = $this->normalizeAiPlayers($response, $roster, $positions, $slots);

        return [
            'players' => $players,
            'note' => $response['note'] ?? '-',
        ];
    }

    private function resolveOpponentName(\App\Models\Matches $match, int $ownTeamId): string
    {
        if ($match->home_team_id === $ownTeamId) {
            return $match->awayTeam?->name ?? $match->opponent_team ?? '-';
        }
        if ($match->away_team_id === $ownTeamId) {
            return $match->homeTeam?->name ?? '-';
        }
        return $match->opponent_team ?? $match->awayTeam?->name ?? '-';
    }

    private function normalizeAiPlayers(array $response, $roster, $positions, array $slots): array
    {
        $entries = collect($response['players'] ?? [])->filter(fn ($entry) => is_array($entry))->values();
        $rosterIds = $roster->pluck('id')->all();
        $fallbackPositionId = (int) $positions->keys()->first();
        $players = [];
        $used = [];

        foreach ($slots as $index => $slot) {
            $entry = $entries->firstWhere('slot_key', $slot['slot_key']) ?? $entries->get($index, []);
            $playerId = (int) ($entry['player_id'] ?? 0);

            if (! in_array($playerId, $rosterIds, true) || in_array($playerId, $used, true)) {
                $candidate = $roster
                    ->sortByDesc(fn (Players $p) => ($p->matchStats->avg('match_rating') ?? 0) + ($p->trainingPerformances->avg('performance_score') ?? 0))
                    ->first(fn (Players $p) => ! in_array($p->id, $used, true));
                $playerId = $candidate?->id;
            }

            if (! $playerId) {
                throw new RuntimeException('Kadro 11 oyuncuya tamamlanamadi.');
            }

            $positionId = (int) ($entry['position_id'] ?? 0);
            if (! $positions->has($positionId)) {
                $positionId = $roster->firstWhere('id', $playerId)?->position_id ?: $fallbackPositionId;
            }

            $used[] = $playerId;
            $players[] = [
                'slot_key' => $slot['slot_key'],
                'field_x' => $slot['field_x'],
                'field_y' => $slot['field_y'],
                'player_id' => $playerId,
                'position_id' => $positionId,
                'is_starting' => true,
                'recommendation_score' => isset($entry['recommendation_score']) ? (float) $entry['recommendation_score'] : null,
            ];
        }

        return $players;
    }
}
