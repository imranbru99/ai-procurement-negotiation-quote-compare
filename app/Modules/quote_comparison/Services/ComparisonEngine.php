<?php

namespace App\Modules\quote_comparison\Services;

use App\Models\Quote;
use App\Modules\multi_currency\Models\ExchangeRate;
use Illuminate\Support\Collection;

class ComparisonEngine
{
    /**
     * @param  Collection<int, Quote>  $quotes
     * @param  array<string, float>  $weights
     * @return array<string, mixed>
     */
    public function compare(
        Collection $quotes,
        array $weights,
        string $baseCurrency = 'USD',
        array $options = [],
    ): array {
        $quotes = $quotes->values();
        $leadTimePenaltyPerDay = (float) ($options['lead_time_penalty_per_day'] ?? 0);

        $rows = $quotes->map(function (Quote $quote) use ($baseCurrency, $leadTimePenaltyPerDay): array {
            $quote->loadMissing('supplier');
            $rate = $this->resolveRate($quote->currency_code, $baseCurrency, $quote);
            $tco = $quote->tcoAmount() * $rate;

            if ($quote->lead_time_days && $leadTimePenaltyPerDay > 0) {
                $tco += (int) $quote->lead_time_days * $leadTimePenaltyPerDay;
            }

            return [
                'quote_id' => $quote->id,
                'supplier_id' => $quote->supplier_id,
                'supplier_name' => $quote->supplier?->name,
                'currency_code' => $quote->currency_code,
                'exchange_rate' => $rate,
                'tco_base' => round($tco, 4),
                'metrics' => [
                    'price' => round($tco, 4),
                    'lead_time' => (int) ($quote->lead_time_days ?? 0),
                    'payment_terms' => $quote->paymentTermsScore(),
                    'warranty' => $quote->warrantyMonths(),
                    'moq' => (float) ($quote->moq ?? 0),
                    'supplier_tier' => $quote->supplierTierScore(),
                ],
            ];
        });

        $criterionMeta = [
            'price' => ['higher_is_better' => false],
            'lead_time' => ['higher_is_better' => false],
            'payment_terms' => ['higher_is_better' => true],
            'warranty' => ['higher_is_better' => true],
            'moq' => ['higher_is_better' => false],
            'supplier_tier' => ['higher_is_better' => true],
        ];

        $normalizedWeights = $this->normalizeWeights($weights, array_keys($criterionMeta));

        $rows = $rows->map(function (array $row) use ($criterionMeta, $normalizedWeights, $rows): array {
            $criterionScores = [];
            $weightedTotal = 0.0;

            foreach ($criterionMeta as $key => $meta) {
                $values = $rows->pluck("metrics.{$key}")->map(fn ($value) => (float) $value);
                $score = $this->criterionScore(
                    (float) $row['metrics'][$key],
                    $values,
                    (bool) $meta['higher_is_better'],
                );
                $weight = $normalizedWeights[$key] ?? 0;
                $criterionScores[$key] = round($score, 2);
                $weightedTotal += $score * $weight;
            }

            $row['criterion_scores'] = $criterionScores;
            $row['weighted_score'] = round($weightedTotal, 2);

            return $row;
        });

        $priceValues = $rows->pluck('tco_base')->map(fn ($v) => (float) $v);
        $medianPrice = $this->median($priceValues);

        $rows = $rows->map(function (array $row) use ($priceValues, $medianPrice): array {
            $row['outliers'] = $this->detectOutliers((float) $row['tco_base'], $priceValues, $medianPrice);

            return $row;
        });

        $best = $rows->sortByDesc('weighted_score')->first();
        $bestValueQuoteId = $best['quote_id'] ?? null;

        if ($best && ($best['outliers']['is_outlier'] ?? false)) {
            $bestValueQuoteId = $rows
                ->reject(fn (array $row): bool => (bool) ($row['outliers']['is_outlier'] ?? false))
                ->sortByDesc('weighted_score')
                ->first()['quote_id'] ?? $bestValueQuoteId;
        }

        return [
            'base_currency' => strtoupper($baseCurrency),
            'weights' => $normalizedWeights,
            'quotes' => $rows->values()->all(),
            'best_value_quote_id' => $bestValueQuoteId,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    protected function resolveRate(string $from, string $to, Quote $quote): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        if ($quote->exchange_rate_to_base && $from !== $to) {
            return (float) $quote->exchange_rate_to_base;
        }

        $direct = ExchangeRate::query()
            ->where('base_code', $from)
            ->where('quote_code', $to)
            ->orderByDesc('as_of')
            ->value('rate');

        if ($direct) {
            return (float) $direct;
        }

        $inverse = ExchangeRate::query()
            ->where('base_code', $to)
            ->where('quote_code', $from)
            ->orderByDesc('as_of')
            ->value('rate');

        if ($inverse && (float) $inverse !== 0.0) {
            return 1 / (float) $inverse;
        }

        return 1.0;
    }

    /**
     * @param  array<string, float>  $weights
     * @param  list<string>  $keys
     * @return array<string, float>
     */
    protected function normalizeWeights(array $weights, array $keys): array
    {
        $filtered = [];

        foreach ($keys as $key) {
            $filtered[$key] = max(0, (float) ($weights[$key] ?? 0));
        }

        $sum = array_sum($filtered) ?: 1.0;

        return collect($filtered)->map(fn (float $weight): float => $weight / $sum)->all();
    }

    /**
     * @param  Collection<int, float|int>  $allValues
     */
    protected function criterionScore(float $value, Collection $allValues, bool $higherIsBetter): float
    {
        $min = (float) $allValues->min();
        $max = (float) $allValues->max();

        if ($max === $min) {
            return 100.0;
        }

        if ($higherIsBetter) {
            return (($value - $min) / ($max - $min)) * 100;
        }

        return (($max - $value) / ($max - $min)) * 100;
    }

    /**
     * @param  Collection<int, float>  $values
     * @return array{is_outlier: bool, z_score: float|null, pct_from_median: float|null}
     */
    protected function detectOutliers(float $value, Collection $values, float $median): array
    {
        $mean = (float) $values->avg();
        $variance = $values->map(fn (float $v): float => ($v - $mean) ** 2)->avg();
        $stdDev = sqrt(max($variance, 0.000001));
        $z = ($value - $mean) / $stdDev;
        $pctFromMedian = $median > 0 ? abs($value - $median) / $median : 0;

        return [
            'is_outlier' => abs($z) > 2 || $pctFromMedian > 0.30,
            'z_score' => round($z, 4),
            'pct_from_median' => round($pctFromMedian, 4),
        ];
    }

    /**
     * @param  Collection<int, float>  $values
     */
    protected function median(Collection $values): float
    {
        $sorted = $values->sort()->values();
        $count = $sorted->count();

        if ($count === 0) {
            return 0.0;
        }

        $middle = (int) floor($count / 2);

        if ($count % 2 === 0) {
            return ((float) $sorted[$middle - 1] + (float) $sorted[$middle]) / 2;
        }

        return (float) $sorted[$middle];
    }
}
