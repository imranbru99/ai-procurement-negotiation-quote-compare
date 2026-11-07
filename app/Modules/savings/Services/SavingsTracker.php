<?php

namespace App\Modules\savings\Services;

use App\Models\Award;
use App\Models\NegotiationThread;
use App\Models\Quote;
use App\Models\SavingsRecord;

class SavingsTracker
{
    public function recordFromNegotiation(
        NegotiationThread $thread,
        float $baselineAmount,
        float $negotiatedAmount,
        ?int $buyerId = null,
        string $currencyCode = 'USD',
    ): SavingsRecord {
        $hard = max(0, $baselineAmount - $negotiatedAmount);

        return SavingsRecord::query()->create([
            'rfq_id' => $thread->rfq_id,
            'negotiation_thread_id' => $thread->id,
            'buyer_id' => $buyerId,
            'baseline_amount' => $baselineAmount,
            'negotiated_amount' => $negotiatedAmount,
            'hard_savings' => $hard,
            'soft_savings' => 0,
            'currency_code' => $currencyCode,
        ]);
    }

    public function recordFromAward(Award $award, Quote $winningQuote, float $baselineAmount): SavingsRecord
    {
        $negotiated = (float) $winningQuote->tcoAmount();

        return SavingsRecord::query()->create([
            'rfq_id' => $award->rfq_id,
            'quote_id' => $winningQuote->id,
            'buyer_id' => $award->awarded_by ?? null,
            'baseline_amount' => $baselineAmount,
            'negotiated_amount' => $negotiated,
            'hard_savings' => max(0, $baselineAmount - $negotiated),
            'soft_savings' => 0,
            'currency_code' => $winningQuote->currency_code,
        ]);
    }
}
