<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AiProvider
{
    public function __construct(
        private string $apiKey,
        private string $model = 'gemini-2.0-flash',
    ) {}

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function name(): string
    {
        return 'gemini';
    }

    public function generate(string $prompt, array $options = []): string
    {
        $model = $options['model'] ?? $this->model;
        $system = $options['system'] ?? 'Sen profesyonel bir futbol analistisin. Türkçe yanıt ver.';

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $system."\n\n".$prompt]]],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.4,
            ],
        ];

        $response = Http::timeout(60)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gemini çağrısı başarısız: '.$response->body());
        }

        return (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
    }

    public function generateJson(string $prompt, array $options = []): array
    {
        $model = $options['model'] ?? $this->model;
        $system = $options['system'] ?? 'Sen profesyonel bir futbol analistisin. Sadece geçerli JSON döndür.';

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $system."\n\n".$prompt]]],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.3,
                'responseMimeType' => 'application/json',
            ],
        ];

        $response = Http::timeout(60)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gemini çağrısı başarısız: '.$response->body());
        }

        $content = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini geçerli JSON döndürmedi.');
        }

        return $decoded;
    }
}
