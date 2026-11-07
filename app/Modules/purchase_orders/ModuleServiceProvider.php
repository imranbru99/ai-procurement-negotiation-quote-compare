<?php

namespace App\Modules\purchase_orders;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('purchase_orders')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('purchase_orders')) {
            return;
        }
    }
}
