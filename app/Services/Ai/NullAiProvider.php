<?php

namespace App\Services\Ai;

class NullAiProvider implements AiProvider
{
    public function isConfigured(): bool
    {
        return false;
    }

    public function name(): string
    {
        return 'none';
    }

    public function generate(string $prompt, array $options = []): string
    {
        throw new AiNotConfiguredException;
    }

    public function generateJson(string $prompt, array $options = []): array
    {
        throw new AiNotConfiguredException;
    }
}
