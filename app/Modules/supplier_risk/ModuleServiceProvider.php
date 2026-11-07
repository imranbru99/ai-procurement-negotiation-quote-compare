<?php

namespace App\Modules\supplier_risk;

use App\Facades\Module;
use App\Modules\supplier_risk\Services\RiskService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RiskService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('supplier_risk')) {
            return;
        }
    }
}
