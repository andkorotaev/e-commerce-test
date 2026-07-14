<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductAttribute>
 */
class ProductAttributeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = fake()->unique()->word();

        return [
            'slug' => Str::slug($slug),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ProductAttribute $attribute) {
            foreach (array_keys(config('localization.locales')) as $locale) {
                ProductAttributeTranslation::factory()
                    ->for($attribute, 'attribute')
                    ->create(['locale' => $locale, 'product_attribute_id' => $attribute->id]);
            }
        });
    }
}
