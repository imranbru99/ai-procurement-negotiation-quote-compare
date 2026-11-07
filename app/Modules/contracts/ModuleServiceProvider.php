<?php

namespace App\Modules\contracts;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('contracts')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('contracts')) {
            return;
        }
    }
}
