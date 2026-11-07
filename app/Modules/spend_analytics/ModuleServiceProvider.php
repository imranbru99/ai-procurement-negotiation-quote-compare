<?php

namespace App\Modules\spend_analytics;

use App\Facades\Module;
use App\Modules\spend_analytics\Services\SpendAnalyticsService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SpendAnalyticsService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('spend_analytics')) {
            return;
        }
    }
}
