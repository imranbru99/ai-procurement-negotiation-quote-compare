<?php

namespace App\Modules\goods_receiving\Services;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\User;
use App\Modules\purchase_orders\Services\POService;
use Illuminate\Support\Facades\DB;

class GoodsReceivingService
{
    public function __construct(
        protected POService $poService,
    ) {}

    /**
     * @param  list<array{po_line_id: int, qty_received: float|string, qty_accepted?: float|string, qty_rejected?: float|string, qc_status?: string}>  $lines
     */
    public function receivePartial(
        PurchaseOrder $po,
        array $lines,
        User $receiver,
        ?string $notes = null,
        ?string $attendancePresenceNote = null,
    ): GoodsReceipt {
        return DB::transaction(function () use ($po, $lines, $receiver, $notes, $attendancePresenceNote) {
            $number = $this->nextNumber($po->tenant_id);

            $grn = GoodsReceipt::query()->create([
                'tenant_id' => $po->tenant_id,
                'po_id' => $po->id,
                'number' => $number,
                'received_by' => $receiver->id,
                'status' => 'posted',
                'received_at' => now(),
                'notes' => $notes,
                'attendance_presence_note' => $attendancePresenceNote,
            ]);

            foreach ($lines as $lineData) {
                $poLine = PurchaseOrderLine::query()->findOrFail($lineData['po_line_id']);
                $qtyReceived = (float) $lineData['qty_received'];
                $qtyAccepted = (float) ($lineData['qty_accepted'] ?? $qtyReceived);
                $qtyRejected = (float) ($lineData['qty_rejected'] ?? max(0, $qtyReceived - $qtyAccepted));

                GoodsReceiptLine::query()->create([
                    'grn_id' => $grn->id,
                    'po_line_id' => $poLine->id,
                    'qty_received' => $qtyReceived,
                    'qty_accepted' => $qtyAccepted,
                    'qty_rejected' => $qtyRejected,
                    'qc_status' => $lineData['qc_status'] ?? 'pass',
                ]);

                $poLine->update([
                    'qty_received' => (float) $poLine->qty_received + $qtyAccepted,
                ]);
            }

            $this->poService->refreshReceiptStatus($po->fresh('lines'));

            return $grn->load('lines');
        });
    }

    protected function nextNumber(int $tenantId): string
    {
        $count = GoodsReceipt::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'GRN-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
