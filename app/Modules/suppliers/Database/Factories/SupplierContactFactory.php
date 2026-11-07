<?php

namespace App\Modules\suppliers\Database\Factories;

use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplierContact>
 */
class SupplierContactFactory extends Factory
{
    protected $model = SupplierContact::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'role' => fake()->randomElement(['Buyer', 'Sales', 'Account Manager']),
            'is_primary' => false,
        ];
    }

    public function forSupplier(Supplier $supplier): static
    {
        return $this->state(fn (): array => [
            'supplier_id' => $supplier->id,
        ]);
    }
}
