<?php

namespace App\Modules\awards\Services;

use App\Models\Award;
use App\Models\AwardLine;
use App\Models\Rfq;
use App\Models\User;
use App\Modules\integrations\Events\AwardCreated;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AwardService
{
    /**
     * Build draft award from quote comparison selections (per RFQ line).
     *
     * @param  list<array{rfq_line_id: int, supplier_id: int, quote_id: int, quote_line_id: int, qty: float|string, unit_price: float|string, currency_code?: string}>  $selections
     */
    public function createFromComparison(Rfq $rfq, array $selections): Award
    {
        return DB::transaction(function () use ($rfq, $selections) {
            $award = Award::query()->create([
                'tenant_id' => $rfq->tenant_id,
                'rfq_id' => $rfq->id,
                'status' => Award::STATUS_PENDING_CONFIRMATION,
            ]);

            foreach ($selections as $selection) {
                AwardLine::query()->create([
                    'award_id' => $award->id,
                    'rfq_line_id' => $selection['rfq_line_id'],
                    'supplier_id' => $selection['supplier_id'],
                    'quote_id' => $selection['quote_id'],
                    'quote_line_id' => $selection['quote_line_id'],
                    'qty' => $selection['qty'],
                    'unit_price' => $selection['unit_price'],
                    'currency_code' => $selection['currency_code'] ?? $rfq->currency_code,
                ]);
            }

            return $award->load('lines');
        });
    }

    public function confirm(Award $award, User $user, ?string $justification = null): Award
    {
        if ($award->status !== Award::STATUS_PENDING_CONFIRMATION && $award->status !== Award::STATUS_DRAFT) {
            throw new RuntimeException('Award cannot be confirmed in its current status.');
        }

        $award->update([
            'status' => Award::STATUS_CONFIRMED,
            'awarded_by' => $user->id,
            'justification' => $justification,
            'awarded_at' => now(),
        ]);

        $award = $award->fresh();
        AwardCreated::dispatch($award);

        return $award;
    }
}
