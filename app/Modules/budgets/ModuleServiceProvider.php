<?php

namespace App\Modules\budgets;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('budgets')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('budgets')) {
            return;
        }
    }
}
