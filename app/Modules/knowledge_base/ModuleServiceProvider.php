<?php

namespace App\Modules\knowledge_base;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (! Module::enabled('knowledge_base') && ! $this->app->runningUnitTests()) {
            return;
        }
    }
}
