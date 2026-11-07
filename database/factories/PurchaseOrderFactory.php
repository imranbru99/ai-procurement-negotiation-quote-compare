<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'supplier_id' => Supplier::factory(),
            'number' => 'PO-'.fake()->unique()->numerify('######'),
            'status' => 'draft',
            'currency_code' => 'USD',
            'subtotal' => 100,
            'tax' => 0,
            'total' => 100,
            'revision' => 1,
        ];
    }
}
