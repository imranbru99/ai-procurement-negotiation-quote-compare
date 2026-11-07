<?php

namespace App\Modules\price_intelligence\Jobs;

use App\Models\Tenant;
use App\Modules\price_intelligence\Services\PriceIntelligenceService;
use App\Services\Tenancy\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeMarketPricesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?int $tenantId = null,
    ) {}

    public function handle(PriceIntelligenceService $service): void
    {
        if ($this->tenantId !== null) {
            TenantContext::set(
                Tenant::query()->findOrFail($this->tenantId)
            );
        }

        $service->scrapeCatalogItems();
    }
}
