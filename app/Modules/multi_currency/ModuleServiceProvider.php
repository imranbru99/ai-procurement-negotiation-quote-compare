<?php

namespace App\Modules\multi_currency;

use App\Facades\Module;
use App\Modules\multi_currency\Models\Currency;
use App\Modules\multi_currency\Models\ExchangeRate;
use App\Modules\multi_currency\Models\TaxRule;
use App\Modules\multi_currency\Policies\CurrencyPolicy;
use App\Modules\multi_currency\Policies\ExchangeRatePolicy;
use App\Modules\multi_currency\Policies\TaxRulePolicy;
use App\Modules\multi_currency\Services\CurrencyService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (! Module::enabled('multi_currency')) {
            return;
        }

        Gate::policy(Currency::class, CurrencyPolicy::class);
        Gate::policy(ExchangeRate::class, ExchangeRatePolicy::class);
        Gate::policy(TaxRule::class, TaxRulePolicy::class);
    }
}
