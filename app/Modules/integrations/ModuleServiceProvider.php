<?php

namespace App\Modules\integrations;

use App\Modules\integrations\Events\AwardCreated;
use App\Modules\integrations\Events\QuoteSubmitted;
use App\Modules\integrations\Listeners\DispatchIntegrationWebhooks;
use App\Modules\integrations\Services\WebhookDispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WebhookDispatcher::class);
    }

    public function boot(): void
    {
        $listener = DispatchIntegrationWebhooks::class;

        Event::listen(QuoteSubmitted::class, [$listener, 'handleQuoteSubmitted']);
        Event::listen(AwardCreated::class, [$listener, 'handleAwardCreated']);
    }
}
