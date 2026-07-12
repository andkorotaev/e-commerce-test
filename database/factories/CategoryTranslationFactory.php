<?php

namespace Database\Factories;

use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

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
