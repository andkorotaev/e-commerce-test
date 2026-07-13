<?php

namespace App\Repositories;

use App\Dto\Product\ProductDto;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository
{
    /**
     * Every product, for the admin list.
     *
     * @return Collection<int, ProductDto>
     */
    public function all(): Collection
    {
        return Product::with(['translations', 'images'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Product $product) => ProductDto::fromModel($product));
    }

    public function find(int $id): ?ProductDto
    {
        $product = Product::with(['translations', 'images'])->find($id);

        return $product ? ProductDto::fromModel($product) : null;
    }

    /**
     * Active products in a given category — for storefront category pages.
     *
     * @return Collection<int, ProductDto>
     */
    public function activeForCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['translations', 'images'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Product $product) => ProductDto::fromModel($product));
    }

    /**
     * A single active product by its translated slug — for the storefront
     * product page. Returns null if no active product has that slug in the
     * given locale.
     */
    public function findBySlug(string $slug, string $locale): ?ProductDto
    {
        $product = Product::where('is_active', true)
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)->where('locale', $locale);
            })
            ->with(['translations', 'images'])
            ->first();

        return $product ? ProductDto::fromModel($product) : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ProductDto
    {
        $product = Product::create($attributes);

        return ProductDto::fromModel($product->load(['translations', 'images']));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): ProductDto
    {
        $product = Product::findOrFail($id);
        $product->update($attributes);

        return ProductDto::fromModel($product->fresh(['translations', 'images']));
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}
