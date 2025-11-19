<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaSeoService
{
    public function generateContent(string $prompt): ?string
    {
        try {
            $endpoint = config('ia.ollama.endpoint', 'http://localhost:11434/api/chat');
            $key      = config('ia.ollama.key', '');
            $model    = config('ia.ollama.model', 'gpt-oss:120b-cloud');
            $timeout  = (int) config('ia.ollama.timeout', 30);

            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'stream'   => false, // ✅ important
            ];

            $request = Http::timeout($timeout)->asJson();
            if (!empty($key)) {
                $request = $request->withToken($key);
            }

            $response = $request->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Ollama SEO Service error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json()['message']['content'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Ollama SEO Service exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
