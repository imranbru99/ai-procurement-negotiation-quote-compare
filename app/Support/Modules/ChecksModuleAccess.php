<?php

namespace App\Support\Modules;

use App\Facades\Module;
use App\Services\Tenancy\TenantContext;

trait ChecksModuleAccess
{
    public static function moduleKey(): string
    {
        return 'dashboard';
    }

    public static function canAccessModule(): bool
    {
        return Module::enabled(static::moduleKey(), TenantContext::getId());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessModule() && parent::shouldRegisterNavigation();
    }
}
