<?php

namespace App\Modules\dashboard;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('dashboard')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('dashboard')) {
            return;
        }
    }
}
