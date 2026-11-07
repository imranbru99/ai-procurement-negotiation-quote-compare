<?php

namespace App\Modules\catalog\Database\Factories;

use App\Modules\catalog\Models\CatalogCategory;
use App\Modules\catalog\Models\CatalogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogItem>
 */
class CatalogItemFactory extends Factory
{
    protected $model = CatalogItem::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-#####')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 1, 5000),
            'currency_code' => 'USD',
            'is_service' => false,
            'is_active' => true,
            'specs' => [],
            'translations' => [],
        ];
    }

    public function forCategory(CatalogCategory $category): static
    {
        return $this->state(fn (): array => [
            'category_id' => $category->id,
            'tenant_id' => $category->tenant_id,
        ]);
    }
}
