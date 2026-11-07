<?php

namespace App\Modules\documents;

use App\Facades\Module;
use App\Modules\documents\Console\DocumentExpiryAlertCommand;
use App\Modules\documents\Models\Document;
use App\Modules\documents\Policies\DocumentPolicy;
use App\Modules\documents\Services\DocumentVaultService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DocumentVaultService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([DocumentExpiryAlertCommand::class]);
        }

        if (! Module::enabled('documents')) {
            return;
        }

        Gate::policy(Document::class, DocumentPolicy::class);
    }
}
