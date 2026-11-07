<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->company(),
            'code' => strtoupper(fake()->unique()->lexify('SUP???')),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'currency_code' => 'USD',
            'is_active' => true,
        ];
    }
}
