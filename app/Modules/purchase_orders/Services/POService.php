<?php

namespace App\Modules\purchase_orders\Services;

use App\Models\Award;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Modules\budgets\Services\BudgetService;
use App\Modules\purchase_orders\Mail\PurchaseOrderSentMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use ImranDev\UnicodePdf\Facades\UnicodePdf;

class POService
{
    public function __construct(
        protected BudgetService $budgetService,
    ) {}

    /**
     * @return Collection<int, PurchaseOrder>
     */
    public function generateFromAward(Award $award, ?int $costCenterId = null): Collection
    {
        $award->load(['lines.rfqLine', 'lines.supplier']);

        return DB::transaction(function () use ($award, $costCenterId) {
            $orders = collect();

            $bySupplier = $award->lines->groupBy('supplier_id');

            foreach ($bySupplier as $supplierId => $lines) {
                $number = $this->nextNumber($award->tenant_id);
                $currency = $lines->first()->currency_code;

                $subtotal = $lines->sum(fn ($line) => (float) $line->qty * (float) $line->unit_price);

                $po = PurchaseOrder::query()->create([
                    'tenant_id' => $award->tenant_id,
                    'award_id' => $award->id,
                    'supplier_id' => $supplierId,
                    'number' => $number,
                    'status' => 'draft',
                    'currency_code' => $currency,
                    'subtotal' => $subtotal,
                    'tax' => 0,
                    'total' => $subtotal,
                    'cost_center_id' => $costCenterId,
                ]);

                $lineNumber = 1;
                foreach ($lines as $awardLine) {
                    PurchaseOrderLine::query()->create([
                        'purchase_order_id' => $po->id,
                        'award_line_id' => $awardLine->id,
                        'rfq_line_id' => $awardLine->rfq_line_id,
                        'description' => $awardLine->rfqLine?->description ?? 'Line '.$lineNumber,
                        'qty' => $awardLine->qty,
                        'unit_price' => $awardLine->unit_price,
                        'currency_code' => $awardLine->currency_code,
                        'line_number' => $lineNumber++,
                    ]);
                }

                if ($costCenterId) {
                    $this->budgetService->commitOnPurchaseOrder($po);
                }

                $orders->push($po->load('lines'));
            }

            return $orders;
        });
    }

    public function revise(PurchaseOrder $po, array $lineUpdates = []): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $lineUpdates) {
            foreach ($lineUpdates as $lineId => $data) {
                PurchaseOrderLine::query()
                    ->where('purchase_order_id', $po->id)
                    ->where('id', $lineId)
                    ->update($data);
            }

            $po->load('lines');

            $subtotal = $po->lines->sum(fn (PurchaseOrderLine $line) => (float) $line->qty * (float) $line->unit_price);

            $po->update([
                'revision' => $po->revision + 1,
                'subtotal' => $subtotal,
                'total' => $subtotal + (float) $po->tax,
            ]);

            return $po->fresh('lines');
        });
    }

    public function send(PurchaseOrder $po): PurchaseOrder
    {
        $po->load(['supplier', 'lines']);

        if ($po->supplier?->email) {
            Mail::to($po->supplier->email)->queue(new PurchaseOrderSentMail($po));
        }

        $po->update(['status' => 'sent']);

        return $po->fresh();
    }

    public function acknowledge(PurchaseOrder $po): PurchaseOrder
    {
        $po->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
        ]);

        return $po->fresh();
    }

    public function pdf(PurchaseOrder $po): mixed
    {
        $po->load(['supplier', 'lines']);

        return UnicodePdf::loadView('pdf.purchase-order', ['purchaseOrder' => $po]);
    }

    protected function nextNumber(int $tenantId): string
    {
        $count = PurchaseOrder::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'PO-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }

    public function refreshReceiptStatus(PurchaseOrder $po): void
    {
        $po->load('lines');
        $allReceived = $po->lines->every(fn (PurchaseOrderLine $line) => (float) $line->qty_received >= (float) $line->qty);
        $anyReceived = $po->lines->contains(fn (PurchaseOrderLine $line) => (float) $line->qty_received > 0);

        if ($allReceived) {
            $po->update(['status' => 'received']);
        } elseif ($anyReceived) {
            $po->update(['status' => 'partially_received']);
        }
    }
}
