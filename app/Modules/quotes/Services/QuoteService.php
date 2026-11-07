<?php

namespace App\Modules\quotes\Services;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Modules\integrations\Events\QuoteSubmitted;
use InvalidArgumentException;

class QuoteService
{
    public function normalizeTotals(Quote $quote): Quote
    {
        $quote->loadMissing('lines');

        $subtotal = 0.0;
        $taxTotal = 0.0;

        foreach ($quote->lines as $line) {
            $lineSubtotal = (float) $line->qty * (float) $line->unit_price;
            $lineTax = $lineSubtotal * ((float) $line->tax_rate / 100);
            $lineTotal = $lineSubtotal + $lineTax;

            if ((float) $line->line_total !== round($lineTotal, 2)) {
                $line->update(['line_total' => round($lineTotal, 2)]);
            }

            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;
        }

        $grandTotal = $subtotal + $taxTotal
            + (float) $quote->shipping_total
            + (float) $quote->other_fees;

        $quote->update([
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($grandTotal, 2),
        ]);

        return $quote->fresh(['lines']);
    }

    public function submit(Quote $quote): Quote
    {
        if (! in_array($quote->status, ['draft', 'revised'], true)) {
            throw new InvalidArgumentException('Quote cannot be submitted in its current status.');
        }

        if ($quote->lines()->count() === 0) {
            throw new InvalidArgumentException('Quote must have at least one line.');
        }

        $this->normalizeTotals($quote);

        $quote->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $quote = $quote->fresh();
        QuoteSubmitted::dispatch($quote);

        return $quote;
    }

    public function recalculateLine(QuoteLine $line): QuoteLine
    {
        $lineSubtotal = (float) $line->qty * (float) $line->unit_price;
        $lineTax = $lineSubtotal * ((float) $line->tax_rate / 100);
        $line->line_total = round($lineSubtotal + $lineTax, 2);
        $line->save();

        return $line;
    }
}
