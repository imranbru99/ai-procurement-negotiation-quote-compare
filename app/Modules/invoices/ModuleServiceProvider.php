<?php

namespace App\Modules\invoices;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('invoices')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('invoices')) {
            return;
        }
    }
}
