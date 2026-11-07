<?php

namespace App\Services\Ai;

use App\Models\AiPromptTemplate;
use App\Models\AiUsageLog;
use App\Services\Settings\SettingsService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Facades\Auth;
use ImranDevBd\AiHub\Data\AiResponse;
use ImranDevBd\AiHub\Facades\AIHub;

class AiService
{
    public function __construct(
        protected SettingsService $settings,
    ) {}

    /**
     * @return array{provider: string, model: string, api_key: ?string, demo: bool}
     */
    public function resolveTenantConfig(?int $tenantId = null): array
    {
        $tenantId ??= TenantContext::getId();

        $demo = (bool) config('ai.demo_mode', true);

        return [
            'provider' => (string) $this->settings->get('ai', 'provider', config('ai.default_provider'), $tenantId),
            'model' => (string) $this->settings->get('ai', 'model', config('ai.default_model'), $tenantId),
            'api_key' => $this->settings->get('ai', 'api_key', null, $tenantId),
            'demo' => $demo || empty($this->settings->get('ai', 'api_key', null, $tenantId)),
        ];
    }

    /**
     * @param  array<string, mixed>  $vars
     */
    public function runPromptTemplate(string $key, array $vars = [], ?string $feature = null): AiResponse
    {
        $tenantId = TenantContext::getId();
        $feature ??= $key;

        $template = AiPromptTemplate::query()
            ->where('key', $key)
            ->where(function ($query) use ($tenantId): void {
                if ($tenantId !== null) {
                    $query->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                } else {
                    $query->whereNull('tenant_id');
                }
            })
            ->orderByDesc('tenant_id')
            ->firstOrFail();

        $config = $this->resolveTenantConfig($tenantId);
        $userPrompt = $template->interpolateUserPrompt($vars);

        if ($config['demo']) {
            $response = new AiResponse(
                content: $this->demoAnalysis($key),
                provider: 'demo',
                model: 'demo',
                promptTokens: 12,
                completionTokens: 48,
                totalTokens: 60,
                costUsd: 0,
                latencyMs: 1,
                success: true,
            );
            $this->logUsage($feature, $response, $config, ['template_key' => $key, 'demo' => true]);

            return $response;
        }

        $pending = AIHub::using($config['provider'], $config['model'], $config['api_key'] ?? null);

        if ($template->system_prompt) {
            $pending = $pending->system($template->system_prompt);
        }

        if (is_array($template->output_schema) && $template->output_schema !== []) {
            $pending = $pending->structured($template->output_schema);
        }

        $response = $pending->prompt($userPrompt)->send();

        $this->logUsage($feature, $response, $config, [
            'template_key' => $key,
        ]);

        return $response;
    }

    public function demoAnalysis(string $templateKey): string
    {
        return match ($templateKey) {
            'quote_analysis' => json_encode([
                'hidden_costs' => ['Customs brokerage fee not itemized'],
                'outliers' => ['Supplier B shipping 42% above median'],
                'unit_mismatches' => ['Line 3 quoted in lbs vs RFQ kg'],
                'summary' => 'Demo analysis — confirm before awarding.',
            ], JSON_THROW_ON_ERROR),
            'negotiation_strategy' => json_encode([
                'target_price' => 9500,
                'walk_away_price' => 11000,
                'batna' => 10200,
                'leverage_points' => ['Competing quote within 5%', 'Volume commitment'],
                'confidence' => 0.72,
            ], JSON_THROW_ON_ERROR),
            'counter_offer' => "Dear supplier,\n\nWe appreciate your quote. Could you reconsider pricing closer to USD 9,500 while holding lead time?\n\nRegards,\nProcurement",
            'supplier_risk' => json_encode([
                'summary' => 'Demo supplier risk review — validate financial, geographic, and compliance signals.',
                'recommended_actions' => ['Request updated bank letter', 'Refresh compliance certificates'],
                'severity' => 'medium',
            ], JSON_THROW_ON_ERROR),
            default => json_encode([
                'message' => 'Demo AI output',
                'template' => $templateKey,
            ], JSON_THROW_ON_ERROR),
        };
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    protected function logUsage(string $feature, AiResponse $response, array $config, array $meta = []): AiUsageLog
    {
        return AiUsageLog::query()->create([
            'tenant_id' => TenantContext::getId(),
            'user_id' => Auth::id(),
            'feature' => $feature,
            'provider' => $response->provider ?? $config['provider'],
            'model' => $response->model ?? $config['model'],
            'prompt_tokens' => $response->promptTokens ?? 0,
            'completion_tokens' => $response->completionTokens ?? 0,
            'cost' => $response->costUsd ?? 0,
            'status' => $response->success ? 'completed' : 'failed',
            'meta' => $meta,
        ]);
    }
}
