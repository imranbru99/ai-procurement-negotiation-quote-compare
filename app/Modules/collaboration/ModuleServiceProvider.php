<?php

namespace App\Modules\collaboration;

use App\Facades\Module;
use App\Modules\collaboration\Services\CommentService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommentService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('collaboration')) {
            return;
        }
    }
}
