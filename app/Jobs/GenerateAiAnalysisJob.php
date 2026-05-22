<?php

namespace App\Jobs;

use App\Services\AiAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAiAnalysisJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $analysisId) {}

    public function handle(AiAnalysisService $service): void
    {
        $service->processQueuedAnalysis($this->analysisId);
    }
}
