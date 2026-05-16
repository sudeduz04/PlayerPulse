<?php

namespace App\Services\Ai;

interface AiProvider
{
    public function isConfigured(): bool;

    public function name(): string;

    public function generate(string $prompt, array $options = []): string;

    public function generateJson(string $prompt, array $options = []): array;
}
