<?php

namespace App\Modules\modules;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('modules')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('modules')) {
            return;
        }
    }
}
