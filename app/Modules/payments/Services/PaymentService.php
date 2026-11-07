<?php

namespace App\Modules\payments\Services;

use App\Models\Payment;
use App\Models\SupplierInvoice;
use App\Modules\budgets\Services\BudgetService;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(
        protected BudgetService $budgetService,
    ) {}

    public function schedule(
        SupplierInvoice $invoice,
        ?\DateTimeInterface $scheduledFor = null,
        ?string $method = null,
        float $earlyDiscount = 0,
    ): Payment {
        $payment = Payment::query()->create([
            'tenant_id' => $invoice->tenant_id,
            'invoice_id' => $invoice->id,
            'amount' => max(0, (float) $invoice->total - $earlyDiscount),
            'currency_code' => $invoice->currency_code,
            'status' => 'scheduled',
            'scheduled_for' => $scheduledFor ?? now()->addDays(7),
            'method' => $method,
            'early_discount_amount' => $earlyDiscount,
        ]);

        return $payment;
    }

    public function markPaid(Payment $payment, ?string $reference = null): Payment
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'reference' => $reference ?? $payment->reference,
        ]);

        $payment->load('supplierInvoice.purchaseOrder');
        $this->budgetService->recordActualOnPayment($payment);

        return $payment->fresh();
    }

    /**
     * @param  Collection<int, Payment>|null  $payments
     */
    public function exportCsv(?Collection $payments = null): string
    {
        $payments ??= Payment::query()->with('supplierInvoice')->get();

        $lines = ['id,invoice_id,amount,currency,status,scheduled_for,paid_at,method,reference'];

        foreach ($payments as $payment) {
            $lines[] = implode(',', [
                $payment->id,
                $payment->invoice_id,
                $payment->amount,
                $payment->currency_code,
                $payment->status,
                $payment->scheduled_for?->toDateTimeString() ?? '',
                $payment->paid_at?->toDateTimeString() ?? '',
                $payment->method ?? '',
                '"'.str_replace('"', '""', $payment->reference ?? '').'"',
            ]);
        }

        return implode("\n", $lines);
    }
}
