<?php

namespace App\Modules\price_intelligence;

use App\Facades\Module;
use App\Modules\price_intelligence\Services\PriceIntelligenceService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PriceIntelligenceService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('price_intelligence')) {
            return;
        }
    }
}
