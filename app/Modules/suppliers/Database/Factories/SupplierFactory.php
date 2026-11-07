<?php

namespace App\Modules\suppliers\Database\Factories;

use App\Models\Supplier;
use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\SupplierCategory;
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
            'name' => fake()->company(),
            'code' => strtoupper(fake()->unique()->bothify('SUP-####')),
            'legal_name' => fake()->company().' '.$this->faker->companySuffix(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'country' => fake()->countryCode(),
            'city' => fake()->city(),
            'currency_code' => 'USD',
            'tier' => fake()->randomElement(SupplierTier::cases()),
            'status' => SupplierStatus::Approved,
            'risk_score' => fake()->numberBetween(1, 100),
            'certifications' => [],
            'metadata' => [],
        ];
    }

    public function forCategory(SupplierCategory $category): static
    {
        return $this->state(fn (): array => [
            'category_id' => $category->id,
            'tenant_id' => $category->tenant_id,
        ]);
    }
}
