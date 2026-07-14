<?php

namespace Database\Factories;

use App\Models\ProductAttributeTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAttributeTranslation>
 */
class ProductAttributeTranslationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => 'uk',
            'name' => fake()->words(1, true),
        ];
    }
}
