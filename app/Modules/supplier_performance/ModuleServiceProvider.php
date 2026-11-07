<?php

namespace App\Modules\supplier_performance;

use App\Facades\Module;
use App\Modules\supplier_performance\Services\ScorecardService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScorecardService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('supplier_performance')) {
            return;
        }
    }
}
