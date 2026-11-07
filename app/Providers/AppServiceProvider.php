<?php

namespace App\Providers;

use App\Console\Commands\DemoResetCommand;
use App\Modules\ai_negotiation\Services\CopilotChatService;
use App\Modules\ai_negotiation\Services\CounterOfferGenerator;
use App\Modules\ai_negotiation\Services\NegotiationSimulator;
use App\Modules\ai_negotiation\Services\NegotiationStrategyService;
use App\Modules\ai_negotiation\Services\QuoteAnalysisService;
use App\Modules\ai_negotiation\Services\QuoteExtractionService;
use App\Modules\awards\Services\AwardService;
use App\Modules\budgets\Services\BudgetService;
use App\Modules\cms\Services\CmsContentService;
use App\Modules\contracts\Console\ContractRenewalAlertCommand;
use App\Modules\contracts\Services\ContractService;
use App\Modules\goods_receiving\Services\GoodsReceivingService;
use App\Modules\inventory\Services\InventoryService;
use App\Modules\invoices\Services\InvoiceService;
use App\Modules\invoices\Services\ThreeWayMatchService;
use App\Modules\payments\Services\PaymentService;
use App\Modules\price_intelligence\Services\PriceIntelligenceService;
use App\Modules\purchase_orders\Services\POService;
use App\Modules\quote_comparison\Services\ComparisonEngine;
use App\Modules\reverse_auctions\Services\AuctionService;
use App\Modules\savings\Services\SavingsTracker;
use App\Modules\spend_analytics\Services\SpendAnalyticsService;
use App\Modules\supplier_performance\Services\ScorecardService;
use App\Modules\supplier_risk\Services\RiskService;
use App\Services\Ai\AiService;
use App\Services\Ai\DemoAiDriver;
use App\Services\Public\ContactFormService;
use App\Services\Public\ModuleManifestCatalog;
use Illuminate\Support\ServiceProvider;
use ImranDevBd\AiHub\Facades\AIHub;
use ImranDevBD\LaravelAdminPanel\Facades\AdminPanel;
use ImranDevBD\LaravelAdminPanel\Panel\Panel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Overwrite package default before routes boot — path avoids Filament /admin.
        $this->app->booting(function (): void {
            AdminPanel::registerPanel(
                Panel::make('admin')
                    ->path('ops-panel')
                    ->brandName('ProcureAI Ops')
                    ->default(true)
            );
        });

        $this->app->singleton(AiService::class);
        $this->app->singleton(DemoAiDriver::class);
        $this->app->singleton(ComparisonEngine::class);
        $this->app->singleton(QuoteExtractionService::class);
        $this->app->singleton(QuoteAnalysisService::class);
        $this->app->singleton(NegotiationStrategyService::class);
        $this->app->singleton(CounterOfferGenerator::class);
        $this->app->singleton(NegotiationSimulator::class);
        $this->app->singleton(CopilotChatService::class);
        $this->app->singleton(SavingsTracker::class);
        $this->app->singleton(BudgetService::class);
        $this->app->singleton(AwardService::class);
        $this->app->singleton(POService::class);
        $this->app->singleton(GoodsReceivingService::class);
        $this->app->singleton(InventoryService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(ThreeWayMatchService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(ContractService::class);
        $this->app->singleton(PriceIntelligenceService::class);
        $this->app->singleton(ScorecardService::class);
        $this->app->singleton(RiskService::class);
        $this->app->singleton(SpendAnalyticsService::class);
        $this->app->singleton(AuctionService::class);
        $this->app->singleton(CmsContentService::class);
        $this->app->singleton(ModuleManifestCatalog::class);
        $this->app->singleton(ContactFormService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ContractRenewalAlertCommand::class,
                DemoResetCommand::class,
            ]);
        }

        foreach (glob(app_path('Modules/*/Database/Migrations')) ?: [] as $migrationPath) {
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }

        if (config('ai.demo_mode')) {
            AIHub::fake($this->app->make(DemoAiDriver::class)->responder());
        }
    }
}
