<?php

namespace App\Modules\savings;

use App\Facades\Module;
use App\Modules\savings\Services\SavingsTracker;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SavingsTracker::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('savings')) {
            return;
        }
    }
}
