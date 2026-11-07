<?php

namespace App\Modules\multi_currency\Database\Seeders;

use App\Modules\multi_currency\Jobs\UpdateExchangeRatesJob;
use App\Modules\multi_currency\Models\Currency;
use Illuminate\Database\Seeder;

class MultiCurrencyModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳'],
        ] as $row) {
            Currency::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'symbol' => $row['symbol'],
                    'decimal_places' => 2,
                    'is_active' => true,
                ],
            );
        }

        (new UpdateExchangeRatesJob)->handle();
    }
}
