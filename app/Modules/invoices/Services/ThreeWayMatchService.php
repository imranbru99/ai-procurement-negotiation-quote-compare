<?php

namespace App\Modules\invoices\Services;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\SupplierInvoice;
use App\Models\ThreeWayMatch;
use App\Models\User;
use App\Services\Settings\SettingsService;

class ThreeWayMatchService
{
    public function __construct(
        protected SettingsService $settings,
    ) {}

    public function match(
        PurchaseOrder $po,
        ?GoodsReceipt $grn,
        SupplierInvoice $invoice,
    ): ThreeWayMatch {
        $tolerance = (float) $this->settings->get('procurement', 'three_way_match_tolerance_percent', 2);

        $poTotal = (float) $po->total;
        $invoiceTotal = (float) $invoice->total;
        $variance = abs($invoiceTotal - $poTotal);
        $variancePercent = $poTotal > 0 ? ($variance / $poTotal) * 100 : 0;

        $status = 'matched';
        $reason = null;

        if ($variancePercent > $tolerance) {
            $status = 'exception';
            $reason = sprintf('Invoice total differs from PO by %.2f%% (tolerance %.2f%%).', $variancePercent, $tolerance);
        }

        if ($grn) {
            $accepted = $grn->lines->sum(fn ($line) => (float) $line->qty_accepted);
            $poQty = $po->lines->sum(fn ($line) => (float) $line->qty);
            if ($accepted < $poQty) {
                $status = 'exception';
                $reason = ($reason ? $reason.' ' : '').'GRN accepted quantity is below PO quantity.';
            }
        }

        return ThreeWayMatch::query()->create([
            'tenant_id' => $po->tenant_id,
            'po_id' => $po->id,
            'grn_id' => $grn?->id,
            'invoice_id' => $invoice->id,
            'status' => $status,
            'variance_amount' => $variance,
            'tolerance_percent' => $tolerance,
            'exception_reason' => $reason,
        ]);
    }

    public function resolve(ThreeWayMatch $match, User $user): ThreeWayMatch
    {
        $match->update([
            'status' => 'resolved',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
        ]);

        return $match->fresh();
    }
}
