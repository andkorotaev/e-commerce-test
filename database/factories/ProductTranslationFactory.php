<?php

namespace Database\Factories;

use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductTranslation>
 */
class ProductTranslationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'locale' => 'uk',
            'name' => $name,
            'slug' => Str::slug($name),
            'h1' => null,
            'meta_title' => null,
            'meta_description' => null,
            'description' => null,
        ];
    }
}
