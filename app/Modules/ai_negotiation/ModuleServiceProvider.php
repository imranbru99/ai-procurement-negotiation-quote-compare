<?php

namespace App\Modules\ai_negotiation;

use App\Facades\Module;
use App\Modules\ai_negotiation\Services\CopilotChatService;
use App\Modules\ai_negotiation\Services\CounterOfferGenerator;
use App\Modules\ai_negotiation\Services\NegotiationSimulator;
use App\Modules\ai_negotiation\Services\NegotiationStrategyService;
use App\Modules\ai_negotiation\Services\QuoteAnalysisService;
use App\Modules\ai_negotiation\Services\QuoteExtractionService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteExtractionService::class);
        $this->app->singleton(QuoteAnalysisService::class);
        $this->app->singleton(NegotiationStrategyService::class);
        $this->app->singleton(CounterOfferGenerator::class);
        $this->app->singleton(NegotiationSimulator::class);
        $this->app->singleton(CopilotChatService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('ai_negotiation')) {
            return;
        }
    }
}
