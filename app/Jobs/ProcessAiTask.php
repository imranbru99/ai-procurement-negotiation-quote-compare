<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Modules\ai_negotiation\Services\CopilotChatService;
use App\Modules\ai_negotiation\Services\NegotiationStrategyService;
use App\Modules\ai_negotiation\Services\QuoteAnalysisService;
use App\Modules\ai_negotiation\Services\QuoteExtractionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessAiTask implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $aiJobId,
    ) {}

    public function handle(
        QuoteExtractionService $extraction,
        QuoteAnalysisService $analysis,
        NegotiationStrategyService $strategy,
        CopilotChatService $copilot,
    ): void {
        $job = AiJob::query()->findOrFail($this->aiJobId);
        $job->update(['status' => 'running']);

        try {
            $output = match ($job->feature) {
                'quote_extraction' => $extraction->processJob($job),
                'quote_analysis' => $analysis->processJob($job),
                'negotiation_strategy' => $strategy->processJob($job),
                'copilot_chat' => $copilot->processJob($job),
                default => ['message' => 'Unsupported AI feature'],
            };

            $job->update([
                'status' => 'completed',
                'output' => $output,
            ]);
        } catch (Throwable $exception) {
            $job->update([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
