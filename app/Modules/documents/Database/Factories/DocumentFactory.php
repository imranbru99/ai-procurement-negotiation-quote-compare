<?php

namespace App\Modules\documents\Database\Factories;

use App\Modules\documents\Enums\DocumentVisibility;
use App\Modules\documents\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['contract', 'certificate', 'general']),
            'version' => 1,
            'visibility' => DocumentVisibility::Tenant,
        ];
    }
}
