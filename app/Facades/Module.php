<?php

namespace App\Facades;

use App\Support\Modules\ModuleManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled(string $key, ?int $tenantId = null)
 * @method static void enable(string $key, ?int $tenantId = null)
 * @method static void disable(string $key, ?int $tenantId = null)
 * @method static list<string> dependencies(string $key)
 * @method static list<string> dependents(string $key)
 * @method static \Illuminate\Support\Collection<int, \App\Models\ModuleDefinition> forTenant(?int $tenantId = null)
 * @method static int syncFromManifests()
 *
 * @see ModuleManager
 */
class Module extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModuleManager::class;
    }
}
