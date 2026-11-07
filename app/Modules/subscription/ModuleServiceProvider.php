<?php

namespace App\Modules\subscription;

use App\Facades\Module;
use App\Modules\subscription\Services\OfflinePaymentDriver;
use App\Modules\subscription\Services\StripeReadyDriver;
use App\Modules\subscription\Services\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SubscriptionService::class);
        $this->app->singleton(OfflinePaymentDriver::class);
        $this->app->singleton(StripeReadyDriver::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('subscription')) {
            return;
        }
    }
}
