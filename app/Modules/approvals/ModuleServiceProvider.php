<?php

namespace App\Modules\approvals;

use App\Models\ApprovalRequest;
use App\Modules\approvals\Listeners\StartApprovalOnRequisitionSubmitted;
use App\Modules\approvals\Services\ApprovalEngine;
use App\Modules\requisitions\Events\RequisitionSubmitted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApprovalEngine::class);
    }

    public function boot(): void
    {
        Event::listen(RequisitionSubmitted::class, StartApprovalOnRequisitionSubmitted::class);

        Gate::define('manage-approval-request', function ($user, ApprovalRequest $request): bool {
            return $user->can('approvals.manage') && $user->tenant_id === $request->tenant_id;
        });
    }
}
