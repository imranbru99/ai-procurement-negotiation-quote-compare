<?php

namespace App\Modules\invoices\Services;

use App\Models\PurchaseOrder;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceLine;
use App\Services\Ai\AiService;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        protected AiService $ai,
    ) {}

    public function createFromPurchaseOrder(PurchaseOrder $po, string $number): SupplierInvoice
    {
        $po->load('lines', 'supplier');

        return DB::transaction(function () use ($po, $number) {
            $invoice = SupplierInvoice::query()->create([
                'tenant_id' => $po->tenant_id,
                'supplier_id' => $po->supplier_id,
                'po_id' => $po->id,
                'number' => $number,
                'status' => 'submitted',
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'currency_code' => $po->currency_code,
                'subtotal' => $po->subtotal,
                'tax' => $po->tax,
                'total' => $po->total,
            ]);

            foreach ($po->lines as $line) {
                SupplierInvoiceLine::query()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'po_line_id' => $line->id,
                    'description' => $line->description,
                    'qty' => $line->qty,
                    'unit_price' => $line->unit_price,
                    'line_total' => round((float) $line->qty * (float) $line->unit_price, 2),
                ]);
            }

            return $invoice->load('lines');
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function extractWithAi(string $rawText): array
    {
        $fields = $this->ai->extractInvoiceFields($rawText);

        return array_merge($fields, ['extracted_via_ai' => true]);
    }
}
