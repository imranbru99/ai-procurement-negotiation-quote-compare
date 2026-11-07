<?php

namespace App\Modules\budgets\Services;

use App\Models\Budget;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Log;

class BudgetService
{
    public function commitOnPurchaseOrder(PurchaseOrder $po): void
    {
        if (! $po->cost_center_id) {
            return;
        }

        $budget = $this->budgetForCostCenter($po->tenant_id, $po->cost_center_id, (int) now()->year);

        if (! $budget) {
            return;
        }

        $budget->increment('committed', (float) $po->total);
        $budget->refresh();

        if ($budget->isExceeded()) {
            Log::warning('Budget exceeded after PO commit', [
                'budget_id' => $budget->id,
                'po_id' => $po->id,
            ]);
        }
    }

    public function recordActualOnPayment(Payment $payment): void
    {
        $po = $payment->supplierInvoice?->purchaseOrder;

        if (! $po?->cost_center_id) {
            return;
        }

        $budget = $this->budgetForCostCenter($payment->tenant_id, $po->cost_center_id, (int) now()->year);

        if (! $budget) {
            return;
        }

        $budget->increment('actual', (float) $payment->amount);
        $budget->refresh();

        if ($budget->isExceeded()) {
            Log::warning('Budget exceeded after payment', [
                'budget_id' => $budget->id,
                'payment_id' => $payment->id,
            ]);
        }
    }

    protected function budgetForCostCenter(int $tenantId, int $costCenterId, int $year): ?Budget
    {
        return Budget::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('cost_center_id', $costCenterId)
            ->where('year', $year)
            ->first();
    }
}
