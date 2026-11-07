<?php

namespace App\Modules\subscription\Services;

use App\Models\ModuleDefinition;
use App\Models\Plan;
use App\Models\PlanModule;
use App\Models\TenantSubscription;

class SubscriptionService
{
    public function activeSubscription(?int $tenantId): ?TenantSubscription
    {
        if ($tenantId === null) {
            return null;
        }

        return TenantSubscription::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan.modules')
            ->latest('id')
            ->first();
    }

    public function planAllowsModule(?int $tenantId, string $moduleKey): bool
    {
        if ($tenantId === null) {
            return true;
        }

        $definition = ModuleDefinition::query()->where('key', $moduleKey)->first();
        if ($definition?->is_core) {
            return true;
        }

        $subscription = $this->activeSubscription($tenantId);
        if (! $subscription) {
            return true;
        }

        $planModule = PlanModule::query()
            ->where('plan_id', $subscription->plan_id)
            ->where('module_key', $moduleKey)
            ->first();

        return $planModule?->enabled === true;
    }

    /**
     * @return list<string>
     */
    public function moduleKeysForPlan(Plan $plan): array
    {
        return PlanModule::query()
            ->where('plan_id', $plan->id)
            ->where('enabled', true)
            ->pluck('module_key')
            ->all();
    }
}
