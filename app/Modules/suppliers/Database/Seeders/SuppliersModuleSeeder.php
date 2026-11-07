<?php

namespace App\Modules\suppliers\Database\Seeders;

use App\Models\Tenant;
use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierCategory;
use App\Services\Tenancy\TenantContext;
use Illuminate\Database\Seeder;

class SuppliersModuleSeeder extends Seeder
{
    public function run(?Tenant $tenant = null): void
    {
        $tenant ??= Tenant::query()->first();

        if (! $tenant) {
            return;
        }

        TenantContext::set($tenant);

        $category = SupplierCategory::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'strategic-partners'],
            ['name' => 'Strategic Partners', 'description' => 'Preferred vendor pool'],
        );

        Supplier::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'DEMO-001'],
            [
                'category_id' => $category->id,
                'name' => 'Demo Industrial Supply',
                'currency_code' => $tenant->currency,
                'email' => 'sales@demo-industrial.test',
            ],
        );

        TenantContext::clear();
    }
}
