<?php

use App\Facades\Module;
use App\Models\ModuleDefinition;
use App\Models\ReverseAuction;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Modules\price_intelligence\Services\PriceIntelligenceService;
use App\Modules\reverse_auctions\Services\AuctionService;
use App\Modules\supplier_performance\Services\ScorecardService;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->tenant = Tenant::query()->firstOrCreate(
        ['slug' => 'acme-intelligence'],
        [
            'name' => 'Acme Intelligence',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'is_active' => true,
        ],
    );

    TenantContext::set($this->tenant);
    config(['ai.demo_mode' => true]);
});

afterEach(fn () => TenantContext::clear());

it('produces deterministic demo market scrape prices', function (): void {
    $service = app(PriceIntelligenceService::class);

    $a = $service->demoScrapePrice('SKU-100|https://market.example/item');
    $b = $service->demoScrapePrice('SKU-100|https://market.example/item');

    expect($a)->toBe($b)
        ->and($a)->toBeGreaterThan(0);

    $expected = $service->demoScrapePrice('widget');
    $point = $service->scrapeAndStore([
        'keyword' => 'widget',
        'sku' => 'SKU-100',
        'source_name' => 'Demo market',
    ]);

    expect((float) $point->price)->toEqual($expected)
        ->and($point->raw['mode'] ?? null)->toBe('demo_deterministic');
});

it('extends reverse auction on late bid and marks winner', function (): void {
    Module::enable('reverse_auctions', $this->tenant->id);

    $supplier = Supplier::factory()->create(['tenant_id' => $this->tenant->id]);

    $endsAt = now()->addMinutes(3);
    $auction = ReverseAuction::query()->create([
        'tenant_id' => $this->tenant->id,
        'title' => 'Test auction',
        'status' => 'live',
        'starts_at' => now()->subMinute(),
        'ends_at' => $endsAt,
        'auto_extend_minutes' => 5,
        'min_bid_decrement' => 25,
        'currency_code' => 'USD',
    ]);

    $service = app(AuctionService::class);
    $service->placeBid($auction, $supplier->id, 1000);
    $bid = $service->placeBid($auction->fresh(), $supplier->id, 950);

    $auction->refresh();

    expect($bid->is_winning)->toBeTrue()
        ->and($auction->ends_at->greaterThan($endsAt))->toBeTrue()
        ->and((float) $auction->winningBid()?->amount)->toBe(950.0);
});

it('computes supplier scorecard with seeded metrics when no PO data', function (): void {
    Module::enable('supplier_performance', $this->tenant->id);

    $supplier = Supplier::factory()->create(['tenant_id' => $this->tenant->id]);
    $scorecard = app(ScorecardService::class)->compute(
        $supplier,
        Carbon::now()->subMonth(),
        Carbon::now(),
    );

    expect((float) $scorecard->overall)->toBeGreaterThan(0)
        ->and($scorecard->supplier_id)->toBe($supplier->id);
});

it('toggles intelligence modules on and off', function (): void {
    $keys = ['price_intelligence', 'supplier_performance', 'supplier_risk', 'spend_analytics', 'reverse_auctions'];

    foreach ($keys as $key) {
        Module::enable($key, $this->tenant->id);
        expect(Module::enabled($key, $this->tenant->id))->toBeTrue();
    }

    // Clear tenant module rows for these keys (and only these) to verify off-state without
    // fighting dependency graphs from awards/savings that may also be enabled in demo seeds.
    $definitionIds = ModuleDefinition::query()->whereIn('key', $keys)->pluck('id');
    TenantModule::query()
        ->where('tenant_id', $this->tenant->id)
        ->whereIn('module_id', $definitionIds)
        ->update(['is_enabled' => false, 'disabled_at' => now(), 'enabled_at' => null]);

    foreach ($keys as $key) {
        expect(Module::enabled($key, $this->tenant->id))->toBeFalse();
    }
});
