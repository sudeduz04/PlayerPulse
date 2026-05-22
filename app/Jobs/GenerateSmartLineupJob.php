<?php

namespace App\Jobs;

use App\Services\SmartLineupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSmartLineupJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $lineupId) {}

    public function handle(SmartLineupService $service): void
    {
        $service->processQueuedLineup($this->lineupId);
    }
}
