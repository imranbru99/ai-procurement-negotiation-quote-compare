<?php

namespace App\Modules\ai_negotiation\Services;

use App\Models\AiJob;
use App\Models\Quote;
use App\Modules\quote_comparison\Services\ComparisonEngine;
use App\Services\Ai\AiService;

class QuoteAnalysisService
{
    public function __construct(
        protected AiService $ai,
        protected ComparisonEngine $comparison,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function analyzeRfqQuotes(int $rfqId, array $weights = []): array
    {
        $quotes = Quote::query()->where('rfq_id', $rfqId)->with('supplier')->get();
        $weights = $weights ?: $this->defaultWeights();
        $baseCurrency = $quotes->first()?->rfq?->currency_code ?? 'USD';

        $comparison = $this->comparison->compare($quotes, $weights, (string) $baseCurrency);

        $response = $this->ai->runPromptTemplate('quote_analysis', [
            'comparison_json' => json_encode($comparison, JSON_THROW_ON_ERROR),
        ], 'quote_analysis');

        return [
            'comparison' => $comparison,
            'ai' => json_decode($response->content, true) ?: ['raw' => $response->content],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function processJob(AiJob $job): array
    {
        $rfqId = (int) ($job->input['rfq_id'] ?? 0);

        return $this->analyzeRfqQuotes($rfqId, (array) ($job->input['weights'] ?? []));
    }

    /**
     * @return array<string, float>
     */
    protected function defaultWeights(): array
    {
        return [
            'price' => 0.35,
            'lead_time' => 0.15,
            'payment_terms' => 0.10,
            'warranty' => 0.10,
            'moq' => 0.10,
            'supplier_tier' => 0.20,
        ];
    }
}
