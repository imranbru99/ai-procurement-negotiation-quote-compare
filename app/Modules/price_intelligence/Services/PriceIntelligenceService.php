<?php

namespace App\Modules\price_intelligence\Services;

use App\Models\CatalogItem;
use App\Models\MarketPricePoint;
use App\Models\PriceAlert;
use App\Services\Tenancy\TenantContext;
use Illuminate\Support\Collection;
use ImranDevBd\AiCatalog\Facades\AiCatalog;
use ImranDevBd\AiCatalog\Models\CatalogGeneration;

class PriceIntelligenceService
{
    public function demoScrapePrice(string $seed, string $currency = 'USD'): float
    {
        unset($currency);

        $hash = abs(crc32(strtolower(trim($seed))));

        return round(($hash % 500_000) / 100 + 9.99, 2);
    }

    /**
     * @param  array{url?: ?string, keyword?: ?string, sku?: ?string, catalog_item_id?: ?int, source_name?: ?string, currency_code?: string}  $input
     */
    public function scrapeAndStore(array $input): MarketPricePoint
    {
        $tenantId = TenantContext::getId();
        $url = $input['url'] ?? null;
        $keyword = $input['keyword'] ?? null;
        $sku = $input['sku'] ?? null;
        $catalogItemId = $input['catalog_item_id'] ?? null;
        $sourceName = $input['source_name'] ?? ($url ? parse_url((string) $url, PHP_URL_HOST) : 'Market');
        $currency = $input['currency_code'] ?? 'USD';

        if ($catalogItemId && $sku === null) {
            $item = CatalogItem::query()->find($catalogItemId);
            $sku = $item?->sku;
            $currency = $item?->currency_code ?? $currency;
        }

        $seed = $url ?? $keyword ?? $sku ?? 'procurement-demo';
        $raw = ['mode' => 'demo'];

        if (! config('ai.demo_mode', true) && ($url || $keyword)) {
            try {
                $generation = $this->generateFromCatalog($url, $keyword);
                $price = $this->extractPriceFromGeneration($generation);
                if ($price !== null) {
                    $raw = [
                        'mode' => 'ai_catalog',
                        'generation_id' => $generation->id,
                        'payload' => $generation->payload,
                    ];

                    return $this->storePoint($tenantId, $catalogItemId, $sku, $url, $sourceName, $price, $currency, $raw);
                }
            } catch (\Throwable $exception) {
                $raw['ai_catalog_error'] = $exception->getMessage();
            }
        }

        $price = $this->demoScrapePrice($seed, $currency);
        $raw['mode'] = 'demo_deterministic';

        return $this->storePoint($tenantId, $catalogItemId, $sku, $url, $sourceName, $price, $currency, $raw);
    }

    protected function generateFromCatalog(?string $url, ?string $keyword): CatalogGeneration
    {
        if ($url) {
            return AiCatalog::fromUrl($url)->generate();
        }

        return AiCatalog::fromKeyword((string) $keyword)->generate();
    }

    protected function extractPriceFromGeneration(CatalogGeneration $generation): ?float
    {
        $payload = $generation->payload ?? [];
        foreach (['price', 'list_price', 'msrp'] as $key) {
            if (isset($payload[$key]) && is_numeric($payload[$key])) {
                return (float) $payload[$key];
            }
        }

        $jsonLd = $payload['jsonLd'] ?? $payload['json_ld'] ?? [];
        if (is_array($jsonLd)) {
            $offers = $jsonLd['offers'] ?? [];
            if (isset($offers['price']) && is_numeric($offers['price'])) {
                return (float) $offers['price'];
            }
        }

        $variants = $payload['variants'] ?? [];
        if (is_array($variants) && isset($variants[0]['price']) && is_numeric($variants[0]['price'])) {
            return (float) $variants[0]['price'];
        }

        $specs = $payload['specs'] ?? [];
        if (is_array($specs) && isset($specs['price']) && is_numeric($specs['price'])) {
            return (float) $specs['price'];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    protected function storePoint(
        ?int $tenantId,
        ?int $catalogItemId,
        ?string $sku,
        ?string $url,
        ?string $sourceName,
        float $price,
        string $currency,
        array $raw,
    ): MarketPricePoint {
        $point = MarketPricePoint::query()->create([
            'tenant_id' => $tenantId,
            'catalog_item_id' => $catalogItemId,
            'sku' => $sku,
            'source_url' => $url,
            'source_name' => $sourceName,
            'price' => $price,
            'currency_code' => $currency,
            'scraped_at' => now(),
            'raw' => $raw,
        ]);

        $this->evaluateAlerts($point);

        return $point;
    }

    public function evaluateAlerts(MarketPricePoint $point): void
    {
        if ($point->catalog_item_id === null) {
            return;
        }

        $item = CatalogItem::query()->find($point->catalog_item_id);
        if (! $item || ! $item->base_price) {
            return;
        }

        $baseline = (float) $item->base_price;
        $deltaPercent = $baseline > 0
            ? (($point->price - $baseline) / $baseline) * 100
            : 0;

        $alerts = PriceAlert::query()
            ->where('catalog_item_id', $point->catalog_item_id)
            ->get();

        foreach ($alerts as $alert) {
            $threshold = (float) $alert->threshold_percent;
            $triggered = match ($alert->direction) {
                'above' => $deltaPercent >= $threshold,
                'below' => $deltaPercent <= -$threshold,
                default => false,
            };

            if ($triggered) {
                $alert->forceFill(['last_triggered_at' => now()])->save();
            }
        }
    }

    public function scrapeCatalogItems(?Collection $items = null): int
    {
        $items ??= CatalogItem::query()->where('is_active', true)->limit(50)->get();
        $count = 0;

        foreach ($items as $item) {
            $this->scrapeAndStore([
                'keyword' => $item->name,
                'sku' => $item->sku,
                'catalog_item_id' => $item->id,
                'source_name' => 'Catalog intelligence',
                'currency_code' => $item->currency_code,
            ]);
            $count++;
        }

        return $count;
    }
}
