# Package integration notes

Only document APIs and behaviors that exist in the installed packages / this app. Prefer Packagist + GitHub links from each package’s `composer.json` / README.

## filament/filament ^5

- Multi-panel setup via `PanelProvider` classes (`AdminPanelProvider`, `AppPanelProvider`, `SupplierPanelProvider`).
- Custom pages use `Filament\Pages\Page` with Livewire actions and Blade views (`resources/views/filament/pages/manage-modules.blade.php`).
- `Filament\Models\Contracts\FilamentUser::canAccessPanel()` gates panel access on `User`.
- Platform UX (attendance devices, notification center, subscription plans) lives under `app/Filament/App` and `app/Filament/Resources`.
- Paths: platform `/admin`, tenant `/app`, supplier `/supplier`.

## spatie/laravel-permission ^8

- `HasRoles` on `User`.
- `RolesAndPermissionsSeeder` seeds granular `{module}.{action}` permissions and role bundles (see README roles matrix).

## spatie/laravel-activitylog ^5

- `LogsActivity` on `User` with `getActivitylogOptions()`.

## laravel/sanctum ^4

- `HasApiTokens` on `User`.
- `/api/v1/*` routes in `routes/api.php` use `auth:sanctum` + `tenant.context` + per-module middleware.

## laravel/pennant ^1

- Installed and reserved for future feature-flag experiments.
- **Module enablement does not use Pennant.** Per-tenant module toggles use the `tenant_modules` table (and plan matrices via `plan_modules` / `PlanSeeder`).
- Default store: `PENNANT_STORE=database`.

## imrandevbd/drutogoti

- [Packagist](https://packagist.org/packages/imrandevbd/drutogoti) · [GitHub](https://github.com/imranbru99/laravel-speed-optimization-drutogoti)
- Enable with `DRUTOGOTI_ENABLED=true`, or leave unset so `config/drutogoti.php` defaults to **on when `APP_ENV=production`**.
- Runtime capture uses the `Drutogoti\CaptureContext` singleton (in-memory queries/cache/cookies during the request, flushed on terminate). **There is no Drutogoti facade.**
- Prefer `DRUTOGOTI_STORAGE_DRIVER=queue` in production so persistence runs after the response; ensure a queue worker is running.
- Dashboard route prefix defaults to `drutogoti` (see config); sample rate via `DRUTOGOTI_SAMPLE_RATE`.

## imrandevbd/laravel-ai-hub / laravel-ai-catalog

- Hub: [Packagist](https://packagist.org/packages/imrandevbd/laravel-ai-hub) · [GitHub](https://github.com/imranbru99/laravel-ai-hub)
- Catalog: [Packagist](https://packagist.org/packages/imrandevbd/laravel-ai-catalog) · [GitHub](https://github.com/imranbru99/laravel-ai-catalog)
- Config published (`config/ai-hub.php`, `config/ai-catalog.php`); wired in `ai_negotiation` and `catalog` modules.
- Demo mode registers `AIHub::fake()` with `DemoAiDriver` when `AI_DEMO_MODE=true` (see [AI_GUIDE.md](AI_GUIDE.md)).

## imrandevbd/laravel-attendance-hub

- [Packagist](https://packagist.org/packages/imrandevbd/laravel-attendance-hub) · [GitHub](https://github.com/imranbru99/laravel-attendance-hub)
- **In package:** device drivers (`VirtualDriver`, `FakeDeviceDriver`), migrations (`attendance_devices`, `attendance_logs`, …), models, optional Filament plugin.
- **In ProcureAI:** `AttendanceDeviceResource`, `StaffAttendancePage`, `AttendanceModuleSeeder` (virtual device), GRN `attendance_presence_note` on goods receipts, tests via `AttendanceHub::fake()` and `AttendanceHub::sync()`.
- Do not duplicate attendance schema in app modules; wrap the hub at the Filament/service layer.

## imrandevbd/laravel-admin-panel

- [Packagist](https://packagist.org/packages/imrandevbd/laravel-admin-panel) · [GitHub](https://github.com/imranbru99/laravel-admin-panel)
- Mounted at **`/ops-panel`** (`ADMIN_PANEL_PREFIX` / `config/admin-panel.php`) so it does **not** collide with Filament’s platform panel at `/admin`.
- Registered in `AppServiceProvider` via `AdminPanel::registerPanel(Panel::make('admin')->path('ops-panel')->…)`, which overwrites the package default before routes boot.
- Package dashboard/theme/health chrome (`DashboardController`, published views under `resources/views/vendor/admin-panel`). Not used for tenant procurement CRUD—that stays on Filament.
- Fluent multi-panel API: `AdminPanel::registerPanel(Panel::make(...)->path(...)->brandName(...))` (see package README).

## imrandevbd/laravel-unicode-pdf, laravel-universal-slug

- Unicode PDF: [Packagist](https://packagist.org/packages/imrandevbd/laravel-unicode-pdf) · [GitHub](https://github.com/imranbru99/laravel-unicode-pdf)
- Universal Slug: [Packagist](https://packagist.org/packages/imrandevbd/laravel-universal-slug) · [GitHub](https://github.com/imrandevbd/laravel-universal-slug)
- Published configs and migrations; used by documents/contracts modules (e.g. multilingual PDF presets in quote comparison exports).
