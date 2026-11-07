<?php

namespace App\Modules\goods_receiving;

use App\Facades\Module;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('goods_receiving')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('goods_receiving')) {
            return;
        }
    }
}
