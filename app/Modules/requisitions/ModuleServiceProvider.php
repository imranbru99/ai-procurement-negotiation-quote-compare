<?php

namespace App\Modules\requisitions;

use App\Models\PurchaseRequisition;
use App\Modules\requisitions\Services\RequisitionService;
use App\Policies\PurchaseRequisitionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RequisitionService::class);
    }

    public function boot(): void
    {
        Gate::policy(PurchaseRequisition::class, PurchaseRequisitionPolicy::class);
    }
}
