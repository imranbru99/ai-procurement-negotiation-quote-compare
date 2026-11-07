# ProcureAI HTTP API (v1)

## Base URL

All routes are under `/api/v1` (see `routes/api.php`).

## Authentication

Use [Laravel Sanctum](https://laravel.com/docs/sanctum) personal access tokens on tenant users (`type = tenant`).

```http
Authorization: Bearer {token}
```

The `tenant.context` middleware sets `TenantContext` from the authenticated user.

## Module gating

| Middleware | Behavior |
|------------|----------|
| `module:integrations` | Entire API group requires the integrations module |
| `module:suppliers` etc. | Each resource endpoint requires its procurement module |

Disabled modules return **404** (see `EnsureModuleEnabled`). Enable `integrations` (and the resource modules) on the tenant before calling the API.

## Resources

| Method | Path | Module |
|--------|------|--------|
| GET | `/suppliers` | suppliers |
| GET | `/suppliers/{id}` | suppliers |
| GET | `/rfqs` | rfq |
| GET | `/rfqs/{id}` | rfq |
| GET | `/quotes` | quotes |
| GET | `/quotes/{id}` | quotes |
| GET | `/purchase-orders` | purchase_orders |
| GET | `/purchase-orders/{id}` | purchase_orders |

Responses use Laravel API Resources under `App\Modules\integrations\Http\Resources`.

## Webhooks

Configure outbound webhooks per tenant (`webhooks` table). Events:

- `quote.submitted` — dispatched when a quote is submitted (`QuoteSubmitted`)
- `award.created` — dispatched when an award is confirmed (`AwardCreated`)

Payloads are POSTed with `X-ProcureAI-Event` and `X-ProcureAI-Signature` (HMAC SHA-256).

## OpenAPI

Machine-readable spec: `storage/api-docs/openapi.yaml`.

Panels (`/admin`, `/app`, `/supplier`, `/ops-panel`) are session/Filament UX—not part of this REST surface.
