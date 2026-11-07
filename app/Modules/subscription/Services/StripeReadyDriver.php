<?php

namespace App\Modules\subscription\Services;

use App\Modules\subscription\Models\SubscriptionInvoice;
use RuntimeException;

class StripeReadyDriver extends BillingService
{
    public function driverKey(): string
    {
        return 'stripe';
    }

    public function charge(SubscriptionInvoice $invoice): bool
    {
        throw new RuntimeException('Stripe billing driver is not configured. Wire STRIPE_KEY to enable.');
    }
}
