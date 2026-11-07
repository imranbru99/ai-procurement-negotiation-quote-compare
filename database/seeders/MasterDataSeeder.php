<?php

namespace Database\Seeders;

use App\Facades\Module;
use App\Models\Tenant;
use App\Modules\catalog\Database\Seeders\CatalogModuleSeeder;
use App\Modules\multi_currency\Database\Seeders\MultiCurrencyModuleSeeder;
use App\Modules\suppliers\Database\Seeders\SuppliersModuleSeeder;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrCreate(
            ['slug' => 'demo-tenant'],
            [
                'name' => 'Demo Tenant',
                'timezone' => 'UTC',
                'locale' => 'en',
                'currency' => 'USD',
                'is_active' => true,
            ],
        );

        foreach (['suppliers', 'catalog', 'multi_currency', 'documents'] as $module) {
            Module::enable($module, $tenant->id);
        }

        $this->callWith(MultiCurrencyModuleSeeder::class, ['tenant' => $tenant]);
        $this->callWith(SuppliersModuleSeeder::class, ['tenant' => $tenant]);
        $this->callWith(CatalogModuleSeeder::class, ['tenant' => $tenant]);
    }
}
