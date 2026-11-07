# ProcureAI Architecture

## Stack

- **Laravel 12** (PHP 8.3+) — application framework
- **Filament 5** — primary admin UX for platform, tenant, and supplier panels
- **MySQL** — single-database multi-tenant model (`tenant_id` columns + global scopes)
- **Spatie Permission / Activitylog** — RBAC and audit trail
- **Sanctum** — API tokens for `/api/v1`
- **ImranDevBd packages** — AI Hub/Catalog, Attendance Hub, Admin Panel (`/ops-panel`), Drutogoti, Unicode PDF, UniversalSlug (see [PACKAGE_NOTES.md](PACKAGE_NOTES.md))

## Multi-tenancy

Tenants are first-class records (`tenants` table). Users carry:

- `tenant_id` (nullable for platform users)
- `type` (`platform`, `tenant`, `supplier`)
- locale / timezone / active flags

`TenantContext` stores the active tenant for the request. `EnsureTenantContext` middleware binds it for Filament `/app` users and API routes. `BelongsToTenant` + `TenantScope` constrain tenant-owned models.

## Module system

Each feature lives under `app/Modules/{key}/` with:

- `module.json` manifest (dependencies, permissions, defaults)
- `ModuleServiceProvider.php` that no-ops unless `Module::enabled($key)`

`ModuleManager` synchronizes manifests into the `modules` table. Per-tenant enablement is tracked in `tenant_modules`. Core modules (`is_core=true`) are always enabled and cannot be disabled.

Dependency rules:

- Enabling a module auto-enables its dependencies
- Disabling is blocked while dependents remain enabled
- Plan matrices (`plan_modules` / `PlanSeeder`) further restrict which keys a tenant may enable

`ModuleServiceProvider` (app provider) registers enabled module providers after boot. Blade `@module('rfq')` wraps UI fragments. **Module toggles do not use Laravel Pennant** (Pennant is installed for future experiments only).

## Panels & surfaces

| Surface | Path | Audience |
|---------|------|----------|
| Marketing | `/` | Public site (CMS-backed) |
| Platform (Filament) | `/admin` | `platform` users with `super_admin` |
| Tenant app (Filament) | `/app` | `tenant` users (branding via `TenantContext`) |
| Supplier (Filament) | `/supplier` | `supplier` users |
| Ops shell | `/ops-panel` | laravel-admin-panel diagnostics / theme chrome |

Module management pages exist on platform and tenant Filament panels.

## Package split

- **Filament** — operational CRUD, module toggles, day-to-day workflows
- **laravel-admin-panel** — lightweight Blade ops shell at `/ops-panel` (not tenant procurement CRUD)
- **AI Hub / AI Catalog / Attendance Hub / Unicode PDF / Universal Slug / Drutogoti** — integrated per module; manifests document permissions independently of package wiring

## Settings

`settings` stores JSON values keyed by `(tenant_id?, group, key)` with `SettingsService` providing cached reads and cache invalidation on writes. Global settings use `tenant_id = null`. AI provider overrides live in the `ai` settings group.

## Security & audit

Spatie Permission drives RBAC (`RolesAndPermissionsSeeder`). Spatie Activitylog records material changes (e.g. on `User`). Sanctum tokens authenticate tenant API clients. Public marketing 404/500 pages use the branded marketing layout outside panel prefixes.
