<?php

namespace App\Modules\backup_gdpr;

use App\Facades\Module;
use App\Modules\backup_gdpr\Console\ExportTenantGdprCommand;
use App\Modules\backup_gdpr\Services\GdprService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GdprService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ExportTenantGdprCommand::class]);
        }

        if (! Module::enabled('backup_gdpr')) {
            return;
        }
    }
}
