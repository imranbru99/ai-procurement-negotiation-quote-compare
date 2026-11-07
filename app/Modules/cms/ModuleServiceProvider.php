<?php

namespace App\Modules\cms;

use App\Facades\Module;
use App\Modules\cms\Services\CmsContentService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CmsContentService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (! Module::enabled('cms') && ! $this->app->runningUnitTests()) {
            return;
        }
    }
}
