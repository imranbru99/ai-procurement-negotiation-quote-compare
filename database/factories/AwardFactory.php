<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Rfq;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Award>
 */
class AwardFactory extends Factory
{
    protected $model = Award::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'rfq_id' => Rfq::factory(),
            'status' => Award::STATUS_DRAFT,
        ];
    }
}
