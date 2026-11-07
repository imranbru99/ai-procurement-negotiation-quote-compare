# Modules

ProcureAI ships as a modular Laravel monolith. Each module has a manifest at `app/Modules/{key}/module.json` (**42 modules**) plus optional migrations, Filament resources, and services.

Enable modules per tenant via `Module::enable($key, $tenantId)` or the **Manage modules** Filament page. Enabling auto-resolves `depends_on`; core modules cannot be disabled. Plan matrices are seeded in `PlanSeeder` (Free / Starter / Pro / Enterprise).

Industry preset recommendations (Manufacturing, Retail, Restaurant, Construction, Healthcare, Agency, E-commerce, Government/NGO, Custom) live in the root [README.md](../README.md). Demo tenant wiring: `DemoTenantsSeeder` (Acme = all; Urban Retail = lean sourcing + AI; BuildRight = Pro P2P).

## Core (always on)

| Key | Purpose | Depends on |
|-----|---------|------------|
| `tenants` | Platform multi-tenancy | — |
| `users` | Roles and tenant users | `tenants` |
| `modules` | Enable/disable feature flags per tenant | `tenants` |
| `settings` | Tenant and AI settings | `tenants` |
| `audit_log` | Activity trail | `tenants` |
| `notifications` | In-app notification hooks | `users` |
| `dashboard` | Tenant home and KPIs | `users` |

## Master data

| Key | Purpose | Depends on |
|-----|---------|------------|
| `suppliers` | Vendor master, contacts, portal users | `tenants`, `users` |
| `catalog` | Items, categories, units, localized names | `suppliers` |
| `multi_currency` | Currencies, FX rates, tax rules | `settings` |
| `documents` | Polymorphic document store | `tenants` |

## Sourcing

| Key | Purpose | Depends on |
|-----|---------|------------|
| `requisitions` | Purchase requisitions | `catalog`, `users` |
| `approvals` | Workflow engine for PR/PO | `requisitions` |
| `rfq` | RFQ lifecycle and supplier invitations | `requisitions`, `suppliers`, `catalog` |
| `quotes` | Supplier quotes and imports | `rfq`, `suppliers`, `catalog` |
| `quote_comparison` | Scoring, TCO, outliers | `quotes` |
| `ai_negotiation` | Extraction, strategy, rounds, copilot | `quote_comparison` |
| `reverse_auctions` | Competitive bidding events | `rfq`, `suppliers` |
| `awards` | Award decisions | `quote_comparison`, `reverse_auctions` |

## Procure-to-pay

| Key | Purpose | Depends on |
|-----|---------|------------|
| `purchase_orders` | PO issuance and revisions | `awards` |
| `contracts` | CLM and renewal alerts | `suppliers`, `awards` |
| `goods_receiving` | GRN and QC | `purchase_orders` |
| `inventory` | Warehouses and stock | `goods_receiving`, `catalog` |
| `invoices` | Supplier invoices, 3-way match | `goods_receiving` |
| `payments` | Payment scheduling | `invoices` |
| `budgets` | Cost centers and budgets | `tenants` |

## Intelligence & risk

| Key | Purpose | Depends on |
|-----|---------|------------|
| `supplier_performance` | Scorecards | `suppliers`, `purchase_orders` |
| `supplier_risk` | Risk flags and assessments | `suppliers` |
| `spend_analytics` | Spend reporting | `purchase_orders`, `payments` |
| `savings` | Hard/soft savings tracking | `spend_analytics`, `ai_negotiation` |
| `price_intelligence` | Market price points | `catalog`, `quotes` |

## Collaboration & platform extensions

| Key | Purpose | Depends on |
|-----|---------|------------|
| `attendance` | Device punch logs (GRN presence) | `users` |
| `collaboration` | Comments and mentions | `users`, `notifications` |
| `tasks` | Task lists | `users`, `collaboration` |
| `integrations` | External connectors + HTTP API gate | `settings` |
| `subscription` | Plans and tenant subscriptions | `tenants` |
| `support` | Help desk tickets | `users` |
| `cms` | Marketing pages and FAQs | `documents` |
| `knowledge_base` | Help articles | `cms` |
| `multi_branch` | Branch hierarchy | `tenants` |
| `multi_language` | Locale packs | `settings` |
| `backup_gdpr` | Export and retention tools | `audit_log`, `settings` |

## Plan matrices (seeded)

| Plan | Typical modules |
|------|-----------------|
| Free | Core + `suppliers`, `catalog`, `requisitions`, `documents`, `multi_currency` |
| Starter | Free + RFQ/quotes path, `approvals`, collab/tasks/support/subscription/integrations/backup/multi_branch, `savings` |
| Pro | Sourcing through P2P (comparison, AI negotiation, awards, PO, GRN, invoices, payments, budgets, contracts) + performance/risk |
| Enterprise | All module keys |

Demo mapping: Urban Retail → Starter (plus explicit AI comparison modules); BuildRight → Pro (+ attendance/inventory extras); Acme → Enterprise (all enabled).
