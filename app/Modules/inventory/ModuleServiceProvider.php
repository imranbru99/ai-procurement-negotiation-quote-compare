<?php

namespace App\Modules\inventory;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('inventory')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('inventory')) {
            return;
        }
    }
}
