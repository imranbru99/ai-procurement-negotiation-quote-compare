<?php

namespace App\Modules\multi_currency\Services;

use App\Modules\multi_currency\Models\ExchangeRate;
use Illuminate\Support\Carbon;
use RuntimeException;

class CurrencyService
{
    public function convert(float $amount, string $from, string $to, ?Carbon $asOf = null): float
    {
        if ($from === $to) {
            return $amount;
        }

        $asOf ??= now()->startOfDay();

        $direct = $this->findRate($from, $to, $asOf);

        if ($direct !== null) {
            return round($amount * (float) $direct->rate, 8);
        }

        $inverse = $this->findRate($to, $from, $asOf);

        if ($inverse !== null && (float) $inverse->rate !== 0.0) {
            return round($amount / (float) $inverse->rate, 8);
        }

        if ($from !== 'USD' && $to !== 'USD') {
            $toUsd = $this->convert($amount, $from, 'USD', $asOf);

            return $this->convert($toUsd, 'USD', $to, $asOf);
        }

        throw new RuntimeException("No exchange rate found for {$from} → {$to} on {$asOf->toDateString()}.");
    }

    protected function findRate(string $base, string $quote, Carbon $asOf): ?ExchangeRate
    {
        return ExchangeRate::query()
            ->where('base_code', $base)
            ->where('quote_code', $quote)
            ->whereDate('as_of', '<=', $asOf)
            ->orderByDesc('as_of')
            ->first();
    }
}
