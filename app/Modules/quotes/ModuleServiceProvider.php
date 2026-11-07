<?php

namespace App\Modules\quotes;

use App\Models\Quote;
use App\Modules\quotes\Services\QuoteService;
use App\Policies\QuotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteService::class);
    }

    public function boot(): void
    {
        Gate::policy(Quote::class, QuotePolicy::class);
    }
}
