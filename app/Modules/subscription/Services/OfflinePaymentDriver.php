<?php

namespace App\Modules\subscription\Services;

use App\Modules\subscription\Models\SubscriptionInvoice;

class OfflinePaymentDriver extends BillingService
{
    public function driverKey(): string
    {
        return 'offline';
    }

    public function charge(SubscriptionInvoice $invoice): bool
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'meta' => array_merge($invoice->meta ?? [], ['method' => 'offline']),
        ]);

        return true;
    }
}
