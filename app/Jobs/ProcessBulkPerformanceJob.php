<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TrainingPerformanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ProcessBulkPerformanceJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 1;

    public function __construct(
        private string $jobUuid,
        private int $trainingId,
        private array $players,
        private int $userId,
    ) {}

    public function handle(TrainingPerformanceService $service): void
    {
        $key = 'bulk:'.$this->jobUuid;
        Cache::put($key, [
            'status' => 'running',
            'processed' => 0,
            'total' => count($this->players),
        ], now()->addHour());

        try {
            $user = User::findOrFail($this->userId);
            $service->bulkUpsert($this->trainingId, $this->players, $user);

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
