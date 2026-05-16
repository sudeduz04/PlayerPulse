<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProvider implements AiProvider
{
    public function __construct(
        private string $apiKey,
        private string $model = 'gpt-4o-mini',
    ) {}

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function name(): string
    {
        return 'openai';
    }

    public function generate(string $prompt, array $options = []): string
    {
        $payload = [
            'model' => $options['model'] ?? $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $options['system'] ?? 'Sen profesyonel bir futbol analistisin. Türkçe yanıt ver.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => $options['temperature'] ?? 0.4,
        ];

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI çağrısı başarısız: '.$response->body());
        }

        return (string) ($response->json('choices.0.message.content') ?? '');
    }

    public function generateJson(string $prompt, array $options = []): array
    {
        $payload = [
            'model' => $options['model'] ?? $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $options['system'] ?? 'Sen profesyonel bir futbol analistisin. Sadece geçerli JSON döndür.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => $options['temperature'] ?? 0.3,
            'response_format' => ['type' => 'json_object'],
        ];

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI çağrısı başarısız: '.$response->body());
        }

        $content = (string) ($response->json('choices.0.message.content') ?? '');
        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('OpenAI geçerli JSON döndürmedi.');
        }

        return $decoded;
    }
}
