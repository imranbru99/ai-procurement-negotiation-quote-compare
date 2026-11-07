<?php

use App\Modules\multi_currency\Models\Currency;
use App\Modules\multi_currency\Models\ExchangeRate;
use App\Modules\multi_currency\Services\CurrencyService;

it('converts amounts using stored exchange rates', function (): void {
    Currency::query()->create(['code' => 'USD', 'name' => 'Dollar', 'symbol' => '$', 'is_active' => true]);
    Currency::query()->create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_active' => true]);

    ExchangeRate::query()->create([
        'base_code' => 'USD',
        'quote_code' => 'EUR',
        'rate' => 0.5,
        'as_of' => now()->toDateString(),
        'source' => 'test',
    ]);

    $service = app(CurrencyService::class);

    expect($service->convert(100, 'USD', 'EUR'))->toBe(50.0);
});
