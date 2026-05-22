<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class JobStatusController extends BaseController
{
    public function show(string $uuid): JsonResponse
    {
        $payload = Cache::get('bulk:'.$uuid);

        if (! $payload) {
            return $this->sendError('Job not found or expired.', 404);
        }

        return $this->sendResponse($payload, 'Job status retrieved.');
    }
}
