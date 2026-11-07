<?php

namespace App\Modules\subscription\Services;

use App\Models\TenantSubscription;
use App\Modules\subscription\Models\SubscriptionInvoice;

abstract class BillingService
{
    abstract public function driverKey(): string;

    public function issueInvoice(TenantSubscription $subscription, int $amountCents, string $currency = 'USD'): SubscriptionInvoice
    {
        return SubscriptionInvoice::query()->create([
            'tenant_subscription_id' => $subscription->id,
            'number' => 'INV-'.now()->format('Ymd').'-'.$subscription->id,
            'amount_cents' => $amountCents,
            'currency' => $currency,
            'status' => 'pending',
            'billing_driver' => $this->driverKey(),
            'issued_at' => now(),
        ]);
    }

    abstract public function charge(SubscriptionInvoice $invoice): bool;
}
