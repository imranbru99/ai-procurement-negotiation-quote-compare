<?php

namespace App\Modules\multi_currency\Jobs;

use App\Modules\multi_currency\Models\Currency;
use App\Modules\multi_currency\Models\ExchangeRate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateExchangeRatesJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, array<string, float>>|null  $demoRates
     */
    public function __construct(
        public ?array $demoRates = null,
    ) {
        $this->demoRates ??= [
            'USD' => [
                'EUR' => 0.92,
                'GBP' => 0.79,
                'BDT' => 110.50,
            ],
            'EUR' => [
                'USD' => 1.09,
            ],
        ];
    }

    public function handle(): void
    {
        $asOf = now()->toDateString();

        foreach ($this->demoRates as $base => $quotes) {
            if (! Currency::query()->whereKey($base)->exists()) {
                continue;
            }

            foreach ($quotes as $quote => $rate) {
                if (! Currency::query()->whereKey($quote)->exists()) {
                    continue;
                }

                $existing = ExchangeRate::query()
                    ->where('base_code', $base)
                    ->where('quote_code', $quote)
                    ->whereDate('as_of', $asOf)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'rate' => $rate,
                        'source' => 'demo',
                    ]);

                    continue;
                }

                ExchangeRate::query()->create([
                    'base_code' => $base,
                    'quote_code' => $quote,
                    'as_of' => $asOf,
                    'rate' => $rate,
                    'source' => 'demo',
                ]);
            }
        }
    }
}
