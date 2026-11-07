<?php

namespace App\Modules\ai_negotiation\Services;

use App\Models\AiJob;
use App\Models\Quote;
use App\Models\Rfq;
use App\Services\Ai\AiService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Facades\Gate;

class CopilotChatService
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function ask(string $question, ?int $userId = null): string
    {
        abort_unless(Gate::allows('ai_negotiation.view'), 403);

        $tenantId = TenantContext::getId();

        $context = [
            'rfqs' => Rfq::query()->latest()->limit(5)->get(['id', 'number', 'title', 'status'])->toArray(),
            'quotes' => Quote::query()->latest()->limit(8)->get(['id', 'number', 'rfq_id', 'status', 'grand_total'])->toArray(),
        ];

        $response = $this->ai->runPromptTemplate('copilot_chat', [
            'question' => $question,
            'context_json' => json_encode($context, JSON_THROW_ON_ERROR),
            'tenant_id' => $tenantId,
            'user_id' => $userId,
        ], 'copilot_chat');

        return $response->content;
    }

    /**
     * @return array<string, mixed>
     */
    public function processJob(AiJob $job): array
    {
        $answer = $this->ask((string) ($job->input['question'] ?? ''), $job->input['user_id'] ?? null);

        return ['answer' => $answer];
    }
}
