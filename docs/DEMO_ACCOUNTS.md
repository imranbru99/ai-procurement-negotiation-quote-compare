# Demo accounts

All demo passwords are **`password`** unless noted. Seeded by `DemoTenantsSeeder` (+ platform user seeder). Marketing demo page highlights are also listed in `config/public.php` → `demo_accounts`.

## Platform

| Role | Email | Panel | Notes |
|------|-------|-------|-------|
| Super Admin | `superadmin@procureai.test` | `/admin` | Filament platform panel; no tenant |

## Tenant login pattern

`{role-local-part}@{tenant-slug}.test` — same password for every account. Local parts map from `DemoTenantsSeeder::ROLE_EMAIL_LOCAL_PARTS`.

### Acme Manufacturing (`acme-manufacturing`) — Enterprise, most modules on

Industry: manufacturing · Plan: `enterprise` · Modules: all non-core keys enabled.

| Role | Email |
|------|-------|
| Owner | `owner@acme-manufacturing.test` |
| Procurement manager | `procurement-manager@acme-manufacturing.test` |
| Buyer | `buyer@acme-manufacturing.test` |
| Requester | `requester@acme-manufacturing.test` |
| Approver | `approver@acme-manufacturing.test` |
| Finance | `finance@acme-manufacturing.test` |
| Warehouse | `warehouse@acme-manufacturing.test` |
| Contract manager | `contract-manager@acme-manufacturing.test` |
| Auditor | `auditor@acme-manufacturing.test` |
| Support | `support@acme-manufacturing.test` |
| Viewer | `viewer@acme-manufacturing.test` |
| Supplier portal 1–3 | `supplier1@acme-manufacturing.test` … `supplier3@acme-manufacturing.test` |

### Urban Retail Co (`urban-retail-co`) — Starter, lean sourcing stack

Industry: retail · Plan: `starter` · Modules: suppliers, catalog, requisitions, approvals, rfq, quotes, quote_comparison, ai_negotiation, savings, multi_currency, documents (+ core; dependency chain may enable related keys such as spend_analytics).

| Role | Email |
|------|-------|
| Owner | `owner@urban-retail-co.test` |
| Procurement manager | `procurement-manager@urban-retail-co.test` |
| Buyer | `buyer@urban-retail-co.test` |
| Requester | `requester@urban-retail-co.test` |
| Approver | `approver@urban-retail-co.test` |
| Finance | `finance@urban-retail-co.test` |
| Warehouse | `warehouse@urban-retail-co.test` |
| Contract manager | `contract-manager@urban-retail-co.test` |
| Auditor | `auditor@urban-retail-co.test` |
| Support | `support@urban-retail-co.test` |
| Viewer | `viewer@urban-retail-co.test` |
| Supplier portal 1–3 | `supplier1@urban-retail-co.test` … `supplier3@urban-retail-co.test` |

### BuildRight Construction (`buildright-construction`) — Pro, P2P + site ops

Industry: construction · Plan: `pro` · Modules: sourcing through P2P (incl. inventory, contracts, attendance, performance/risk) as listed in `DemoTenantsSeeder`.

| Role | Email |
|------|-------|
| Owner | `owner@buildright-construction.test` |
| Procurement manager | `procurement-manager@buildright-construction.test` |
| Buyer | `buyer@buildright-construction.test` |
| Requester | `requester@buildright-construction.test` |
| Approver | `approver@buildright-construction.test` |
| Finance | `finance@buildright-construction.test` |
| Warehouse | `warehouse@buildright-construction.test` |
| Contract manager | `contract-manager@buildright-construction.test` |
| Auditor | `auditor@buildright-construction.test` |
| Support | `support@buildright-construction.test` |
| Viewer | `viewer@buildright-construction.test` |
| Supplier portal 1–3 | `supplier1@buildright-construction.test` … `supplier3@buildright-construction.test` |

## Roles reference

Permission bundles: `RolesAndPermissionsSeeder` (also summarized in the root README). Supplier portal users use the `supplier` role on `/supplier`.

## Subscription plans (seeded)

| Plan | Slug | Typical tenant |
|------|------|----------------|
| Free | `free` | — |
| Starter | `starter` | Urban Retail Co |
| Pro | `pro` | BuildRight Construction |
| Enterprise | `enterprise` | Acme Manufacturing |

## Reset demo data

```bash
php artisan demo:reset
```

Runs `migrate:fresh --seed` in `local` / `staging` (or when `DEMO_RESET_ENABLED=true`). Scheduled nightly at 02:00 in those environments (`routes/console.php`).
