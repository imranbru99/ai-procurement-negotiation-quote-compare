<?php

namespace App\Modules\catalog\Database\Seeders;

use App\Models\Tenant;
use App\Modules\catalog\Models\CatalogCategory;
use App\Modules\catalog\Models\CatalogItem;
use App\Modules\catalog\Models\CatalogUnit;
use App\Services\Tenancy\TenantContext;
use Illuminate\Database\Seeder;

class CatalogModuleSeeder extends Seeder
{
    public function run(?Tenant $tenant = null): void
    {
        $tenant ??= Tenant::query()->first();

        if (! $tenant) {
            return;
        }

        TenantContext::set($tenant);

        CatalogUnit::query()->firstOrCreate(
            ['tenant_id' => null, 'code' => 'EA'],
            ['name' => 'Each'],
        );

        $unit = CatalogUnit::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'BOX'],
            ['name' => 'Box'],
        );

        $category = CatalogCategory::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'general'],
            ['name' => 'General'],
        );

        CatalogItem::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'sku' => 'DEMO-SKU-001'],
            [
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'name' => 'Demo catalog item',
                'base_price' => 100,
                'currency_code' => $tenant->currency,
            ],
        );

        TenantContext::clear();
    }
}
