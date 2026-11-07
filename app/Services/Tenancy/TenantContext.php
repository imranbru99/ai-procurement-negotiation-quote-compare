<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;

class TenantContext
{
    protected static ?Tenant $tenant = null;

    public static function set(Tenant $tenant): void
    {
        static::$tenant = $tenant;
    }

    public static function setById(int $tenantId): ?Tenant
    {
        $tenant = Tenant::query()->find($tenantId);

        if ($tenant) {
            static::set($tenant);
        }

        return $tenant;
    }

    public static function get(): ?Tenant
    {
        return static::$tenant;
    }

    public static function getId(): ?int
    {
        return static::$tenant?->id;
    }

    public static function clear(): void
    {
        static::$tenant = null;
    }
}
