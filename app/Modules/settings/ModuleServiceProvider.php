<?php

namespace App\Modules\settings;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('settings')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('settings')) {
            return;
        }
    }
}
