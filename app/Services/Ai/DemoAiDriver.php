<?php

namespace App\Services\Ai;

use Closure;

class DemoAiDriver
{
    public function __construct(
        protected AiService $aiService,
    ) {}

    public function responder(): Closure
    {
        return function (array $payload, string $provider): string {
            $prompt = (string) ($payload['messages'][count($payload['messages']) - 1]['content'] ?? '');
            $key = 'general';

            foreach (['quote_analysis', 'negotiation_strategy', 'counter_offer', 'quote_extraction', 'copilot'] as $candidate) {
                if (str_contains(strtolower($prompt), str_replace('_', ' ', $candidate))) {
                    $key = $candidate;
                    break;
                }
            }

            return $this->aiService->demoAnalysis($key);
        };
    }
}
