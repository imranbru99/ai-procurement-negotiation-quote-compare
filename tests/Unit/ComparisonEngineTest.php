<?php

use App\Models\Quote;
use App\Modules\quote_comparison\Services\ComparisonEngine;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\Supplier;
use Illuminate\Support\Collection;

it('ranks lower TCO higher when price weight dominates', function (): void {
    $supplierA = new Supplier(['name' => 'Alpha', 'tier' => SupplierTier::Preferred]);
    $supplierB = new Supplier(['name' => 'Beta', 'tier' => SupplierTier::Standard]);

    $quotes = Collection::make([
        tap(new Quote([
            'currency_code' => 'USD',
            'grand_total' => 12000,
            'lead_time_days' => 14,
            'payment_terms' => 'Net 30',
            'warranty_terms' => '12 months',
            'moq' => 10,
        ]), function (Quote $q) use ($supplierA): void {
            $q->id = 1;
            $q->setRelation('supplier', $supplierA);
        }),
        tap(new Quote([
            'currency_code' => 'USD',
            'grand_total' => 10000,
            'lead_time_days' => 21,
            'payment_terms' => 'Net 60',
            'warranty_terms' => '6 months',
            'moq' => 5,
        ]), function (Quote $q) use ($supplierB): void {
            $q->id = 2;
            $q->setRelation('supplier', $supplierB);
        }),
    ]);

    $engine = new ComparisonEngine;
    $result = $engine->compare($quotes, ['price' => 1], 'USD');

    expect($result['best_value_quote_id'])->toBe(2)
        ->and($result['quotes'][1]['weighted_score'])->toBeGreaterThan($result['quotes'][0]['weighted_score']);
});

it('flags statistical price outliers', function (): void {
    $supplier = new Supplier(['name' => 'S', 'tier' => SupplierTier::Standard]);

    $quotes = Collection::make([
        [9000, 1],
        [9200, 2],
        [9100, 3],
        [18000, 4],
    ])->map(fn (array $pair) => tap(new Quote([
        'currency_code' => 'USD',
        'grand_total' => $pair[0],
        'lead_time_days' => 10,
        'payment_terms' => 'Net 30',
        'warranty_terms' => '12 months',
        'moq' => 1,
    ]), function (Quote $q) use ($supplier, $pair): void {
        $q->id = $pair[1];
        $q->setRelation('supplier', $supplier);
    }));

    $engine = new ComparisonEngine;
    $outlier = $engine->compare($quotes, ['price' => 1], 'USD');

    $high = collect($outlier['quotes'])->firstWhere('quote_id', 4);
    expect($high['outliers']['is_outlier'])->toBeTrue();
});
