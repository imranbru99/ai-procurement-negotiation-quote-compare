<?php

namespace App\Modules\catalog;

use App\Facades\Module;
use App\Modules\catalog\Models\CatalogCategory;
use App\Modules\catalog\Models\CatalogItem;
use App\Modules\catalog\Policies\CatalogCategoryPolicy;
use App\Modules\catalog\Policies\CatalogItemPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        if (! Module::enabled('catalog')) {
            return;
        }

        Gate::policy(CatalogItem::class, CatalogItemPolicy::class);
        Gate::policy(CatalogCategory::class, CatalogCategoryPolicy::class);
    }
}
