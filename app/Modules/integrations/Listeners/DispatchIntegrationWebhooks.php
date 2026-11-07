<?php

namespace App\Modules\integrations\Listeners;

use App\Facades\Module;
use App\Modules\integrations\Events\AwardCreated;
use App\Modules\integrations\Events\QuoteSubmitted;
use App\Modules\integrations\Services\WebhookDispatcher;

class DispatchIntegrationWebhooks
{
    public function __construct(protected WebhookDispatcher $dispatcher) {}

    public function handleQuoteSubmitted(QuoteSubmitted $event): void
    {
        if (! Module::enabled('integrations', $event->quote->tenant_id)) {
            return;
        }

        $quote = $event->quote;

        $this->dispatcher->dispatch(WebhookDispatcher::EVENT_QUOTE_SUBMITTED, (int) $quote->tenant_id, [
            'quote_id' => $quote->id,
            'rfq_id' => $quote->rfq_id,
            'supplier_id' => $quote->supplier_id,
            'status' => $quote->status,
            'grand_total' => $quote->grand_total,
        ]);
    }

    public function handleAwardCreated(AwardCreated $event): void
    {
        if (! Module::enabled('integrations', $event->award->tenant_id)) {
            return;
        }

        $award = $event->award;

        $this->dispatcher->dispatch(WebhookDispatcher::EVENT_AWARD_CREATED, (int) $award->tenant_id, [
            'award_id' => $award->id,
            'rfq_id' => $award->rfq_id,
            'status' => $award->status,
        ]);
    }
}
