<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MatchStatsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ProcessBulkMatchStatsJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 1;

    public function __construct(
        private string $jobUuid,
        private int $matchId,
        private array $players,
        private int $userId,
    ) {}

    public function handle(MatchStatsService $service): void
    {
        $key = 'bulk:'.$this->jobUuid;
        Cache::put($key, [
            'status' => 'running',
            'processed' => 0,
            'total' => count($this->players),
        ], now()->addHour());

        try {
            $user = User::findOrFail($this->userId);
            $service->bulkUpsert($this->matchId, $this->players, $user);

            Cache::put($key, [
                'status' => 'completed',
                'processed' => count($this->players),
                'total' => count($this->players),
            ], now()->addHour());
        } catch (Throwable $e) {
            Cache::put($key, [
                'status' => 'failed',
                'processed' => 0,
                'total' => count($this->players),
                'error_message' => $e->getMessage(),
            ], now()->addHour());
            throw $e;
        }
    }
}
