<?php

namespace App\Services\Settings;

use App\Models\Setting;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected function cacheKey(?int $tenantId, string $group, string $key): string
    {
        return sprintf('settings.%s.%s.%s', $tenantId ?? 'global', $group, $key);
    }

    public function get(string $group, string $key, mixed $default = null, ?int $tenantId = null): mixed
    {
        $tenantId ??= TenantContext::getId();

        return Cache::remember(
            $this->cacheKey($tenantId, $group, $key),
            now()->addHour(),
            function () use ($group, $key, $default, $tenantId) {
                $setting = Setting::query()
                    ->withoutGlobalScopes()
                    ->where('group', $group)
                    ->where('key', $key)
                    ->where('tenant_id', $tenantId)
                    ->first();

                if (! $setting) {
                    $setting = Setting::query()
                        ->withoutGlobalScopes()
                        ->where('group', $group)
                        ->where('key', $key)
                        ->whereNull('tenant_id')
                        ->first();
                }

                return $setting?->value ?? $default;
            }
        );
    }

    public function set(string $group, string $key, mixed $value, ?int $tenantId = null): Setting
    {
        $tenantId ??= TenantContext::getId();

        $setting = Setting::query()
            ->withoutGlobalScopes()
            ->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'group' => $group,
                    'key' => $key,
                ],
                ['value' => $value]
            );

        Cache::forget($this->cacheKey($tenantId, $group, $key));

        return $setting;
    }

    public function forget(string $group, string $key, ?int $tenantId = null): void
    {
        $tenantId ??= TenantContext::getId();

        Setting::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('group', $group)
            ->where('key', $key)
            ->delete();

        Cache::forget($this->cacheKey($tenantId, $group, $key));
    }
}
