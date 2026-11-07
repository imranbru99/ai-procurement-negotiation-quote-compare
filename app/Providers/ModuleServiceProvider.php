<?php

namespace App\Providers;

use App\Facades\Module;
use App\Services\Settings\SettingsService;
use App\Support\Modules\ModuleManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class);
        $this->app->singleton(SettingsService::class);
    }

    public function boot(): void
    {
        Blade::if('module', function (string $key): bool {
            return Module::enabled($key);
        });

        $this->app->booted(function (): void {
            $this->app->make(ModuleManager::class)->registerEnabledModuleProviders();
        });
    }
}
