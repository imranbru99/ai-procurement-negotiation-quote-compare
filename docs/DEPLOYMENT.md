# Deployment

## Requirements

- PHP 8.3+, Composer 2.x
- MySQL 8+ or MariaDB 10.6+
- Node.js 20+ for Vite assets
- Redis optional (cache/queue; defaults to database)

## Environment

Copy `.env.example` to `.env` and set:

- `APP_NAME=ProcureAI`, `APP_URL`, `APP_KEY` (`php artisan key:generate`)
- Database credentials (`DB_DATABASE=procure_ai`, `DB_USERNAME=procure`, `DB_PASSWORD=procure_secret` match Compose defaults)
- `AI_DEMO_MODE=true` for offline AI demos (no API keys); `false` in real production AI usage
- `QUEUE_CONNECTION=database` (or `redis` in production)
- `DRUTOGOTI_ENABLED` — when unset, Drutogoti enables itself if `APP_ENV=production` (see `config/drutogoti.php`). Set explicitly for local profiling.
- `ADMIN_PANEL_PREFIX=ops-panel` — laravel-admin-panel path (avoids Filament `/admin`)

## Install (bare metal / VM)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm ci && npm run build
```

## Docker Compose

The repo ships a multi-stage `Dockerfile` (PHP 8.4-FPM + Composer + Vite assets) and `docker-compose.yml` with:

| Service | Role |
|---------|------|
| `app` | PHP-FPM application |
| `nginx` | Serves `public/` (port **8080** → 80 by default) |
| `mysql` | MySQL 8.0 (`procure` / `procure_secret` / `procure_ai`) |
| `redis` | Cache / queue broker |
| `queue` | `php artisan queue:work` |
| `scheduler` | `php artisan schedule:work` |

### First-time setup

```bash
cp .env.example .env
# Prefer APP_URL=http://localhost:8080 when using Compose
make setup
```

`make setup` builds images, starts services, runs `composer install`, `key:generate`, `migrate --seed`, and `npm ci && npm run build`.

### Day-to-day

```bash
make up       # build + start detached
make down     # stop stack
make fresh    # migrate:fresh --seed
make test     # artisan test inside app
make lint     # pint --test (+ phpstan if configured)
make shell    # bash in app container
make logs     # follow compose logs
```

### Compose networking notes

- From the **host**, MySQL is on `DB_PORT` / `DB_PUBLISH_PORT` (default **3306**); Redis on **6379**.
- From **containers**, use `DB_HOST=mysql`, `DB_PORT=3306`, `REDIS_HOST=redis` (already set in `docker-compose.yml`).
- Override published ports with `APP_HTTP_PORT`, `DB_PUBLISH_PORT`, `REDIS_PUBLISH_PORT`.

### Production image tips

- Build without bind mounts for immutable deploys: `docker compose build` then run `app` / `nginx` / `queue` / `scheduler` against managed MySQL/Redis.
- Set `APP_ENV=production`, `APP_DEBUG=false`, strong secrets, and prefer `QUEUE_CONNECTION=redis` + `DRUTOGOTI_STORAGE_DRIVER=queue`.
- Run `php artisan config:cache`, `route:cache`, and `view:cache` in the release pipeline after `npm run build`.

## Demo / staging refresh

```bash
php artisan demo:reset
```

Guarded to `local` and `staging` unless `DEMO_RESET_ENABLED=true`. Scheduled in `routes/console.php` at 02:00.

## Production checklist

- `APP_ENV=production`, `APP_DEBUG=false`
- `AI_DEMO_MODE=false` and configure AI Hub keys in tenant settings (unless running a demo environment)
- Drutogoti: leave `DRUTOGOTI_ENABLED` unset (defaults on in production) or set `true`; ensure a queue worker processes storage jobs
- Configure mail, S3 (`FILESYSTEM_DISK=s3` if used)
- Run queue worker: `php artisan queue:work` (or Compose `queue` service)
- Run scheduler: `php artisan schedule:work` / cron `schedule:run` (or Compose `scheduler` service)
- Opcache enabled; `php artisan config:cache`, `route:cache`, `view:cache`

## Filament / admin surfaces

| Surface | Path |
|---------|------|
| Marketing site | `/` |
| Platform (Filament) | `/admin` |
| Tenant app | `/app` |
| Supplier portal | `/supplier` |
| Ops shell (laravel-admin-panel) | `/ops-panel` |

Branded public `404` / `500` views live under `resources/views/public/errors/` (and `resources/views/errors/500.blade.php` for the framework fallback).

## Further reading

- [ARCHITECTURE.md](ARCHITECTURE.md) · [MODULES.md](MODULES.md) · [AI_GUIDE.md](AI_GUIDE.md) · [API.md](API.md) · [DEMO_ACCOUNTS.md](DEMO_ACCOUNTS.md) · root [README.md](../README.md)
