<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'price' => fake()->randomFloat(2, 100, 5000),
            'old_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            foreach (array_keys(config('localization.locales')) as $locale) {
                ProductTranslation::factory()
                    ->for($product)
                    ->create(['locale' => $locale]);
            }
        });
    }
}
