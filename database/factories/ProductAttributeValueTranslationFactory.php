<?php

namespace Database\Factories;

use App\Models\ProductAttributeValueTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAttributeValueTranslation>
 */
class ProductAttributeValueTranslationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => 'uk',
            'value' => fake()->word(),
        ];
    }
}
