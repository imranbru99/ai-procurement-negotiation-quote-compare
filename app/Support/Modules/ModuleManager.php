<?php

namespace App\Support\Modules;

use App\Models\ModuleDefinition;
use App\Models\TenantModule;
use App\Modules\subscription\Services\SubscriptionService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ModuleManager
{
    protected ?Collection $definitions = null;

    public function enabled(string $key, ?int $tenantId = null): bool
    {
        if (! $this->hasModuleTables()) {
            return $this->enabledFromManifest($key);
        }

        $definition = $this->findDefinition($key);

        if (! $definition) {
            return $this->enabledFromManifest($key);
        }

        if ($definition->is_core) {
            return true;
        }

        $tenantId ??= TenantContext::getId();

        if ($tenantId === null) {
            return (bool) $definition->default_enabled;
        }

        $tenantModule = TenantModule::query()
            ->where('tenant_id', $tenantId)
            ->where('module_id', $definition->id)
            ->first();

        if ($tenantModule) {
            if (! $tenantModule->is_enabled) {
                return false;
            }

            return $this->planAllowsModule($tenantId, $key);
        }

        $defaultEnabled = (bool) $definition->default_enabled;

        if (! $defaultEnabled) {
            return false;
        }

        return $this->planAllowsModule($tenantId, $key);
    }

    public function enable(string $key, ?int $tenantId = null): void
    {
        $definition = $this->requireDefinition($key);

        if ($definition->is_core) {
            return;
        }

        $tenantId ??= TenantContext::getId();

        if ($tenantId === null) {
            throw new RuntimeException('Tenant context is required to enable modules.');
        }

        if (! $this->planAllowsModule($tenantId, $key)) {
            throw new RuntimeException("Module [{$key}] is not included on the tenant's subscription plan.");
        }

        foreach ($this->dependencies($key) as $dependency) {
            if (! $this->enabled($dependency, $tenantId)) {
                $this->enable($dependency, $tenantId);
            }
        }

        TenantModule::query()->updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'module_id' => $definition->id,
            ],
            [
                'is_enabled' => true,
                'enabled_at' => now(),
                'disabled_at' => null,
            ]
        );
    }

    public function disable(string $key, ?int $tenantId = null): void
    {
        $definition = $this->requireDefinition($key);

        if ($definition->is_core) {
            throw new RuntimeException("Core module [{$key}] cannot be disabled.");
        }

        $tenantId ??= TenantContext::getId();

        if ($tenantId === null) {
            throw new RuntimeException('Tenant context is required to disable modules.');
        }

        $activeDependents = collect($this->dependents($key))
            ->filter(fn (string $dependent): bool => $this->enabled($dependent, $tenantId));

        if ($activeDependents->isNotEmpty()) {
            throw new RuntimeException(
                'Cannot disable module because dependents are enabled: '.$activeDependents->implode(', ')
            );
        }

        TenantModule::query()->updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'module_id' => $definition->id,
            ],
            [
                'is_enabled' => false,
                'enabled_at' => null,
                'disabled_at' => now(),
            ]
        );
    }

    /**
     * @return list<string>
     */
    public function dependencies(string $key): array
    {
        $definition = $this->findDefinition($key);

        return $definition?->depends_on ?? [];
    }

    /**
     * @return list<string>
     */
    public function dependents(string $key): array
    {
        return $this->allDefinitions()
            ->filter(function (ModuleDefinition $module) use ($key): bool {
                return in_array($key, $module->depends_on ?? [], true);
            })
            ->pluck('key')
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, ModuleDefinition>
     */
    public function forTenant(?int $tenantId = null): Collection
    {
        $tenantId ??= TenantContext::getId();

        return $this->allDefinitions()->map(function (ModuleDefinition $module) use ($tenantId) {
            $module->setAttribute(
                'is_enabled_for_tenant',
                $this->enabled($module->key, $tenantId)
            );

            return $module;
        });
    }

    public function syncFromManifests(): int
    {
        $paths = File::glob(app_path('Modules/*/module.json')) ?: [];
        $synced = 0;

        foreach ($paths as $path) {
            $manifest = json_decode(File::get($path), true);

            if (! is_array($manifest) || empty($manifest['key'])) {
                continue;
            }

            ModuleDefinition::query()->updateOrCreate(
                ['key' => $manifest['key']],
                [
                    'name' => $manifest['name'] ?? $manifest['key'],
                    'description' => $manifest['description'] ?? null,
                    'icon' => $manifest['icon'] ?? null,
                    'category' => $manifest['category'] ?? 'procurement',
                    'depends_on' => $manifest['depends_on'] ?? [],
                    'is_core' => (bool) ($manifest['is_core'] ?? false),
                    'default_enabled' => (bool) ($manifest['default_enabled'] ?? false),
                    'sort' => (int) ($manifest['sort'] ?? 100),
                    'permissions' => $manifest['permissions'] ?? [],
                    'navigation' => $manifest['navigation'] ?? [],
                ]
            );

            $synced++;
        }

        $this->definitions = null;

        return $synced;
    }

    /**
     * @return list<string>
     */
    public function manifestKeys(): array
    {
        return collect(File::glob(app_path('Modules/*/module.json')) ?: [])
            ->map(function (string $path): ?string {
                $manifest = json_decode(File::get($path), true);

                return is_array($manifest) ? ($manifest['key'] ?? null) : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function registerEnabledModuleProviders(): void
    {
        foreach ($this->manifestKeys() as $key) {
            $providerClass = $this->providerClassForKey($key);

            if ($providerClass && class_exists($providerClass)) {
                app()->register($providerClass);
            }
        }
    }

    protected function providerClassForKey(string $key): ?string
    {
        return 'App\\Modules\\'.$key.'\\ModuleServiceProvider';
    }

    protected function findDefinition(string $key): ?ModuleDefinition
    {
        return $this->allDefinitions()->firstWhere('key', $key);
    }

    protected function requireDefinition(string $key): ModuleDefinition
    {
        $definition = $this->findDefinition($key);

        if (! $definition) {
            throw new RuntimeException("Module [{$key}] is not defined.");
        }

        return $definition;
    }

    /**
     * @return Collection<int, ModuleDefinition>
     */
    protected function allDefinitions(): Collection
    {
        if ($this->definitions === null) {
            if (! $this->hasModuleTables()) {
                $this->definitions = collect();
            } else {
                $this->definitions = ModuleDefinition::query()->orderBy('sort')->get();
            }
        }

        return $this->definitions;
    }

    protected function hasModuleTables(): bool
    {
        return Schema::hasTable('modules') && Schema::hasTable('tenant_modules');
    }

    protected function enabledFromManifest(string $key): bool
    {
        $manifest = $this->readManifest($key);

        if ($manifest === null) {
            return false;
        }

        if ((bool) ($manifest['is_core'] ?? false)) {
            return true;
        }

        return (bool) ($manifest['default_enabled'] ?? false);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function readManifest(string $key): ?array
    {
        $path = app_path('Modules/'.$key.'/module.json');

        if (! File::exists($path)) {
            return null;
        }

        $manifest = json_decode(File::get($path), true);

        return is_array($manifest) ? $manifest : null;
    }

    protected function planAllowsModule(?int $tenantId, string $key): bool
    {
        if ($tenantId === null) {
            return true;
        }

        return app(SubscriptionService::class)->planAllowsModule($tenantId, $key);
    }
}
