<?php

namespace App\Modules\ai_negotiation\Services;

use App\Models\AiJob;
use App\Models\NegotiationStrategy;
use App\Models\NegotiationThread;
use App\Services\Ai\AiService;

class NegotiationStrategyService
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function generateForThread(NegotiationThread $thread): NegotiationStrategy
    {
        $response = $this->ai->runPromptTemplate('negotiation_strategy', [
            'rfq_id' => $thread->rfq_id,
            'supplier_id' => $thread->supplier_id,
        ], 'negotiation_strategy');

        $payload = json_decode($response->content, true) ?: [];

        return NegotiationStrategy::query()->updateOrCreate(
            ['thread_id' => $thread->id],
            [
                'target_price' => $payload['target_price'] ?? null,
                'walk_away_price' => $payload['walk_away_price'] ?? null,
                'batna' => $payload['batna'] ?? null,
                'leverage_points' => $payload['leverage_points'] ?? [],
                'concession_plan' => $payload['concession_plan'] ?? [],
                'reasoning' => $payload['reasoning'] ?? $response->content,
                'confidence' => $payload['confidence'] ?? null,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function processJob(AiJob $job): array
    {
        $thread = NegotiationThread::query()->findOrFail((int) $job->input['thread_id']);
        $strategy = $this->generateForThread($thread);

        return ['strategy_id' => $strategy->id];
    }
}
