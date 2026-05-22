<?php

namespace App\Jobs;

use App\Services\FixtureService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportFixtureFileJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 1;

    public function __construct(private int $fixtureImportId) {}

    public function handle(FixtureService $service): void
    {
        $service->processStoredFile($this->fixtureImportId);
    }
}
