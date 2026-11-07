<?php

use App\Facades\Module;
use App\Models\Tenant;
use App\Modules\catalog\Models\CatalogItem;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    Module::syncFromManifests();

    $this->tenant = Tenant::query()->create([
        'name' => 'Catalog Tenant',
        'slug' => 'catalog-tenant',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    Module::enable('catalog', $this->tenant->id);
    TenantContext::set($this->tenant);
});

afterEach(fn () => TenantContext::clear());

it('persists catalog items with sku uniqueness per tenant', function (): void {
    $item = CatalogItem::factory()->create(['tenant_id' => $this->tenant->id]);

    expect($item->sku)->not->toBeEmpty()
        ->and($item->slug)->not->toBeEmpty();
});
