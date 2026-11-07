<?php

namespace App\Modules\quote_comparison;

use App\Facades\Module;
use App\Modules\quote_comparison\Services\ComparisonEngine;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ComparisonEngine::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('quote_comparison')) {
            return;
        }
    }
}
