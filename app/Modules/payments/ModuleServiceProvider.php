<?php

namespace App\Modules\payments;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('payments')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('payments')) {
            return;
        }
    }
}
