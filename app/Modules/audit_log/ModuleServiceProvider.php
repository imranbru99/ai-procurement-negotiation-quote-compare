<?php

namespace App\Modules\audit_log;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('audit_log')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('audit_log')) {
            return;
        }
    }
}
