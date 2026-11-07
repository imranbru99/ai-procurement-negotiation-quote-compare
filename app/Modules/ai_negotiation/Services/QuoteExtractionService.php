<?php

namespace App\Modules\ai_negotiation\Services;

use App\Models\AiJob;
use App\Models\QuoteExtraction;
use App\Services\Ai\AiService;
use Illuminate\Support\Str;

class QuoteExtractionService
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function extractFromText(string $rawText, ?int $quoteId = null): QuoteExtraction
    {
        $lines = $this->parseLines($rawText);

        $extraction = QuoteExtraction::query()->create([
            'quote_id' => $quoteId,
            'source_type' => 'paste',
            'raw_text' => $rawText,
            'extracted' => ['lines' => $lines],
            'status' => 'pending_review',
        ]);

        return $extraction;
    }

    /**
     * @return array<string, mixed>
     */
    public function processJob(AiJob $job): array
    {
        $text = (string) ($job->input['raw_text'] ?? '');
        $response = $this->ai->runPromptTemplate('quote_extraction', [
            'raw_text' => $text,
        ], 'quote_extraction');

        return [
            'lines' => $this->parseLines($text),
            'ai' => $response->content,
        ];
    }

    /**
     * @return list<array{description: string, qty: ?float, unit_price: ?float}>
     */
    public function parseLines(string $rawText): array
    {
        $lines = [];

        foreach (preg_split('/\R+/', trim($rawText)) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || Str::startsWith($line, '#')) {
                continue;
            }

            if (preg_match('/^(?<desc>.+?)\s+[qtyx]*\s*(?<qty>\d+(?:\.\d+)?)\s*[@x]\s*(?<price>\d+(?:\.\d+)?)/i', $line, $matches)) {
                $lines[] = [
                    'description' => trim($matches['desc']),
                    'qty' => (float) $matches['qty'],
                    'unit_price' => (float) $matches['price'],
                ];

                continue;
            }

            $lines[] = [
                'description' => $line,
                'qty' => null,
                'unit_price' => null,
            ];
        }

        return $lines;
    }
}
