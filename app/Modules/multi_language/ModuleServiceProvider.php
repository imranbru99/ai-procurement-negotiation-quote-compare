<?php

namespace App\Modules\multi_language;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('multi_language')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('multi_language')) {
            return;
        }
    }
}
