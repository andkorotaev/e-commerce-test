<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductAttributeValue>
 */
class ProductAttributeValueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = fake()->unique()->word();

        return [
            'product_attribute_id' => ProductAttribute::factory(),
            'slug' => Str::slug($slug),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ProductAttributeValue $value) {
            foreach (array_keys(config('localization.locales')) as $locale) {
                ProductAttributeValueTranslation::factory()
                    ->for($value, 'attributeValue')
                    ->create(['locale' => $locale, 'product_attribute_value_id' => $value->id]);
            }
        });
    }
}
