<?php

namespace App\Modules\supplier_performance\Services;

use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\SupplierScorecard;
use App\Services\Tenancy\TenantContext;
use Carbon\CarbonInterface;

class ScorecardService
{
    public function compute(
        Supplier|\App\Modules\suppliers\Models\Supplier|int $supplier,
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
    ): SupplierScorecard {
        $supplierModel = is_int($supplier)
            ? Supplier::query()->findOrFail($supplier)
            : $supplier;

        $metrics = $this->metricsFromOperations($supplierModel, $periodStart, $periodEnd);

        if ($metrics === null) {
            $metrics = $this->seededMetrics($supplierModel->id);
        }

        return SupplierScorecard::query()->updateOrCreate(
            [
                'tenant_id' => TenantContext::getId() ?? $supplierModel->tenant_id,
                'supplier_id' => $supplierModel->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            array_merge($metrics, [
                'notes' => $metrics['notes'] ?? 'Auto-computed scorecard',
            ])
        );
    }

    /**
     * @return array<string, float|string>|null
     */
    protected function metricsFromOperations(
        Supplier|\App\Modules\suppliers\Models\Supplier $supplier,
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
    ): ?array {
        $pos = PurchaseOrder::query()
            ->where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->with(['lines', 'goodsReceipts'])
            ->get();

        if ($pos->isEmpty()) {
            $quotes = Quote::query()
                ->where('supplier_id', $supplier->id)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            if ($quotes === 0) {
                return null;
            }
        }

        $onTimeScores = [];
        $qualityScores = [];
        $priceScores = [];
        $responseScores = [];

        foreach ($pos as $po) {
            $grn = $po->goodsReceipts->sortByDesc('received_at')->first();
            if ($grn?->received_at) {
                $days = $po->created_at->diffInDays($grn->received_at);
                $onTimeScores[] = min(100, max(0, 100 - max(0, $days - 14) * 5));
            }

            $ordered = (float) $po->lines->sum('qty');
            $received = (float) $po->lines->sum('qty_received');
            if ($ordered > 0) {
                $qualityScores[] = min(100, ($received / $ordered) * 100);
            }

            if ($po->total) {
                $priceScores[] = min(100, max(40, 100 - ((float) $po->total / 1000)));
            }
        }

        $quotes = Quote::query()
            ->where('supplier_id', $supplier->id)
            ->whereBetween('submitted_at', [$periodStart, $periodEnd])
            ->with('rfq')
            ->get();

        foreach ($quotes as $quote) {
            if ($quote->rfq?->published_at && $quote->submitted_at) {
                $hours = $quote->rfq->published_at->diffInHours($quote->submitted_at);
                $responseScores[] = min(100, max(20, 100 - $hours));
            }
        }

        $onTime = $this->average($onTimeScores, 75);
        $quality = $this->average($qualityScores, 80);
        $price = $this->average($priceScores, 70);
        $response = $this->average($responseScores, 72);
        $overall = round(($onTime + $quality + $price + $response) / 4, 2);

        return [
            'on_time_score' => $onTime,
            'quality_score' => $quality,
            'price_score' => $price,
            'responsiveness_score' => $response,
            'overall' => $overall,
            'notes' => 'Computed from POs, GRNs, and quotes',
        ];
    }

    /**
     * @param  list<float|int>  $values
     */
    protected function average(array $values, float $fallback): float
    {
        if ($values === []) {
            return $fallback;
        }

        return round(array_sum($values) / count($values), 2);
    }

    /**
     * @return array<string, float|string>
     */
    protected function seededMetrics(int $supplierId): array
    {
        $hash = abs(crc32('scorecard-'.$supplierId));
        $base = 55 + ($hash % 40);

        return [
            'on_time_score' => round($base + ($hash % 7), 2),
            'quality_score' => round($base - ($hash % 5), 2),
            'price_score' => round($base + (($hash >> 3) % 9), 2),
            'responsiveness_score' => round($base + (($hash >> 5) % 6), 2),
            'overall' => round($base + (($hash >> 7) % 4), 2),
            'notes' => 'Seeded demo metrics — enable PO/quote data for live scoring',
        ];
    }
}
