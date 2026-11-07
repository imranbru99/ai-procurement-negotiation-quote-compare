<?php

namespace App\Modules\tasks;

use App\Facades\Module;
use App\Modules\tasks\Console\SendTaskRemindersCommand;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SendTaskRemindersCommand::class]);
        }

        if (! Module::enabled('tasks')) {
            return;
        }
    }
}
