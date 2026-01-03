<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';

    // Defined fallback hierarchy
    protected array $models = [
        "openai/gpt-oss-120b",
        "openai/gpt-oss-20b",
        "openai/gpt-oss-safeguard-20b",
    ];

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', env('GROQ_API_KEY'));
    }

    /**
     * Public entry point for the chat.
     */
    public function chat(array $history)
    {
        // 1. Prepare System Context
        $systemMessage = [
            'role' => 'system',
            'content' => "You are 'Color Bot', an expert AI assistant for a Color Lookup website.
            Analyze Hex, RGB, and HSL codes. Explain color mood/psychology.
            Suggest palettes. Return output in Markdown."
        ];

        // Ensure system message is first
        if (($history[0]['role'] ?? '') !== 'system') {
            array_unshift($history, $systemMessage);
        }

        // 2. Start Recursive Attempt (Start at index 0)
        return $this->attemptChat($history, 0);
    }

    /**
     * Recursive method to try models sequentially on failure.
     */
    protected function attemptChat(array $history, int $modelIndex)
    {
        // Stop if we've run out of models to try
        if (!isset($this->models[$modelIndex])) {
            Log::error("GroqService: All models failed. Last attempted index: " . ($modelIndex - 1));
            return "I am currently experiencing high traffic on all channels. Please try again in a moment.";
        }

        $currentModel = $this->models[$modelIndex];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $currentModel,
                    'messages' => $history,
                    'temperature' => 0.5,
                    'max_tokens' => 500,
                ]);

            // Success case
            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            // Check for specific error codes (429 = Rate Limit, 5xx = Server Error)
            if (in_array($response->status(), [429, 500, 502, 503, 504])) {
                Log::warning("Groq Model [$currentModel] failed with status {$response->status()}. Switching to next model...");

                // RECURSIVE CALL: Try next model
                return $this->attemptChat($history, $modelIndex + 1);
            }

            // If it's a 400 or 401 (Bad Request/Unauthorized), do not retry.
            Log::error("Groq API Permanent Error ({$response->status()}): " . $response->body());
            return "Configuration error. Please check the logs.";

        } catch (\Exception $e) {
            // Handle timeouts or connection exceptions similarly
            Log::warning("Groq Connection Exception with [$currentModel]: " . $e->getMessage());

            // RECURSIVE CALL: Try next model on connection failure
            return $this->attemptChat($history, $modelIndex + 1);
        }
    }
}
