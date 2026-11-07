<?php

namespace Database\Factories;

use App\Models\Rfq;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rfq>
 */
class RfqFactory extends Factory
{
    protected $model = Rfq::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'number' => 'RFQ-'.fake()->unique()->numerify('######'),
            'title' => fake()->sentence(3),
            'status' => 'open',
            'currency_code' => 'USD',
        ];
    }
}
