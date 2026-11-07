<?php

namespace App\Modules\suppliers;

use App\Facades\Module;
use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierCategory;
use App\Modules\suppliers\Policies\SupplierCategoryPolicy;
use App\Modules\suppliers\Policies\SupplierPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (! Module::enabled('suppliers')) {
            return;
        }

        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(SupplierCategory::class, SupplierCategoryPolicy::class);
    }
}
