<?php

namespace App\Modules\spend_analytics\Services;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;

class SpendAnalyticsService
{
    /**
     * @return array{
     *     total_spend: float,
     *     by_supplier: array<int, array{supplier_id: int, name: string, total: float}>,
     *     by_department: array<int, array{cost_center_id: ?int, name: string, total: float}>,
     *     maverick_spend: float,
     *     tail_suppliers: list<string>,
     * }
     */
    public function snapshot(?int $tenantId = null): array
    {
        $query = PurchaseOrder::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['issued', 'acknowledged', 'partially_received', 'received', 'closed']);

        $orders = $query->with(['supplier', 'costCenter'])->get();
        $totalSpend = (float) $orders->sum(fn (PurchaseOrder $po) => (float) $po->total);

        $bySupplier = $orders->groupBy('supplier_id')->map(function (Collection $group, $supplierId) {
            /** @var PurchaseOrder $first */
            $first = $group->first();

            return [
                'supplier_id' => (int) $supplierId,
                'name' => $first->supplier?->name ?? 'Unknown',
                'total' => round((float) $group->sum(fn (PurchaseOrder $po) => (float) $po->total), 2),
            ];
        })->sortByDesc('total')->values()->all();

        $byDepartment = $orders->groupBy('cost_center_id')->map(function (Collection $group, $centerId) {
            /** @var PurchaseOrder $first */
            $first = $group->first();

            return [
                'cost_center_id' => $centerId ? (int) $centerId : null,
                'name' => $first->costCenter?->name ?? 'Unassigned',
                'total' => round((float) $group->sum(fn (PurchaseOrder $po) => (float) $po->total), 2),
            ];
        })->sortByDesc('total')->values()->all();

        $maverickSpend = $this->maverickSpend($orders);
        $tailSuppliers = $this->tailSpendSuppliers($bySupplier);

        return [
            'total_spend' => round($totalSpend, 2),
            'by_supplier' => $bySupplier,
            'by_department' => $byDepartment,
            'maverick_spend' => $maverickSpend,
            'tail_suppliers' => $tailSuppliers,
        ];
    }

    /**
     * @param  Collection<int, PurchaseOrder>  $orders
     */
    protected function maverickSpend(Collection $orders): float
    {
        $maverick = $orders->filter(function (PurchaseOrder $po) use ($orders) {
            $supplierOrders = $orders->where('supplier_id', $po->supplier_id);

            return $supplierOrders->count() === 1 && (float) $po->total < 5000;
        });

        return round((float) $maverick->sum(fn (PurchaseOrder $po) => (float) $po->total), 2);
    }

    /**
     * @param  list<array{supplier_id: int, name: string, total: float}>  $bySupplier
     * @return list<string>
     */
    protected function tailSpendSuppliers(array $bySupplier): array
    {
        if ($bySupplier === []) {
            return [];
        }

        $cutoff = (int) ceil(count($bySupplier) * 0.2);

        return collect($bySupplier)
            ->sortBy('total')
            ->take(max(1, $cutoff))
            ->pluck('name')
            ->all();
    }
}
