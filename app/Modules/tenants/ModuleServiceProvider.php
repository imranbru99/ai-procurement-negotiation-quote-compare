<?php

namespace App\Modules\tenants;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('tenants')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('tenants')) {
            return;
        }
    }
}
