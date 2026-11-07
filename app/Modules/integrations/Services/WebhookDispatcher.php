<?php

namespace App\Modules\integrations\Services;

use App\Modules\integrations\Models\Webhook;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class WebhookDispatcher
{
    public const EVENT_QUOTE_SUBMITTED = 'quote.submitted';

    public const EVENT_AWARD_CREATED = 'award.created';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(string $event, int $tenantId, array $payload): void
    {
        $webhooks = Webhook::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            if (! $webhook->listensTo($event)) {
                continue;
            }

            $body = json_encode([
                'event' => $event,
                'tenant_id' => $tenantId,
                'occurred_at' => now()->toIso8601String(),
                'data' => $payload,
            ], JSON_THROW_ON_ERROR);

            $signature = hash_hmac('sha256', $body, $webhook->secret);

            $this->client()->withHeaders([
                'X-ProcureAI-Event' => $event,
                'X-ProcureAI-Signature' => $signature,
            ])->post($webhook->url, json_decode($body, true, 512, JSON_THROW_ON_ERROR));
        }
    }

    protected function client(): PendingRequest
    {
        return Http::timeout(10)->acceptJson();
    }
}
