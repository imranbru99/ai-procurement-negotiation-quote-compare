# AI guide

## Demo mode (default)

Set in `.env`:

```env
AI_DEMO_MODE=true
```

When enabled:

- `config/ai.php` sets `demo_mode`
- `AppServiceProvider` registers `AIHub::fake()` with `DemoAiDriver`
- `AiService::runPromptTemplate()` returns deterministic demo text without external API calls
- Filament quote comparison shows a demo banner when `config('ai.demo_mode')` is true

No OpenAI/Anthropic keys are required for demos or CI.

Related env keys (see `.env.example`):

```env
AI_HUB_DEFAULT_PROVIDER=openai
AI_HUB_OPENAI_API_KEY=
AI_HUB_ANTHROPIC_API_KEY=
```

## Tenant overrides

Per-tenant AI provider, model, and API key can be stored via `SettingsService` (`ai` group). If a key is missing, `AiService` falls back to demo responses even when `AI_DEMO_MODE=false`.

## Prompt templates

Global templates are seeded by `AiPromptTemplateSeeder` (keys: `quote_extraction`, `quote_analysis`, `negotiation_strategy`, `counter_offer`, `negotiation_simulator`, `procurement_copilot`).

Tenants may override templates with `tenant_id` set on `ai_prompt_templates`.

## Features map

| Template key | Module feature |
|--------------|----------------|
| `quote_extraction` | Paste/PDF quote ingestion |
| `quote_analysis` | Comparison insights and outlier flags |
| `negotiation_strategy` | BATNA and concession planning |
| `counter_offer` | Email draft counter-offers |
| `negotiation_simulator` | What-if round outcomes |
| `procurement_copilot` | In-app chat assistant |

These capabilities ship with the `ai_negotiation` module (depends on `quote_comparison` → `quotes` → …). Usage is logged in `ai_usage_logs` via `AiService::logUsage()`.

## Production

1. Set `AI_DEMO_MODE=false`
2. Configure `AI_HUB_*` keys in `.env` or tenant settings
3. Monitor `ai_usage_logs` for cost and latency
4. Ensure queue workers run if AI jobs are dispatched asynchronously

Package wiring: [PACKAGE_NOTES.md](PACKAGE_NOTES.md) · deployment: [DEPLOYMENT.md](DEPLOYMENT.md).
