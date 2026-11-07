<?php

namespace App\Modules\ai_negotiation\Services;

use App\Services\Ai\AiService;

class NegotiationSimulator
{
    public function __construct(
        protected AiService $ai,
    ) {}

    /**
     * @param  array<string, mixed>  $scenario
     * @return array<string, mixed>
     */
    public function estimate(array $scenario): array
    {
        $response = $this->ai->runPromptTemplate('negotiation_simulator', [
            'scenario_json' => json_encode($scenario, JSON_THROW_ON_ERROR),
        ], 'negotiation_simulator');

        $decoded = json_decode($response->content, true);

        return [
            'estimate' => $decoded ?: ['raw' => $response->content],
            'disclaimer' => 'AI estimate only — requires human validation before action.',
        ];
    }
}
