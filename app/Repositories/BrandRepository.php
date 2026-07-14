<?php

namespace App\Repositories;

use App\Dto\Brand\BrandDto;
use App\Models\Brand;
use Illuminate\Support\Collection;

class BrandRepository
{
    /**
     * @return Collection<int, BrandDto>
     */
    public function all(): Collection
    {
        return Brand::orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => BrandDto::fromModel($brand));
    }

    /**
     * Active brands only — for the storefront/admin product-form dropdown.
     *
     * @return Collection<int, BrandDto>
     */
    public function active(): Collection
    {
        return Brand::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => BrandDto::fromModel($brand));
    }

    public function find(int $id): ?BrandDto
    {
        $brand = Brand::find($id);

        return $brand ? BrandDto::fromModel($brand) : null;
    }

    /**
     * Active brands actually used by an active product within the given
     * category ids — the storefront listing page's brand filter facet.
     *
     * @param  array<int, int>  $categoryIds
     * @return Collection<int, BrandDto>
     */
    public function facetForCategories(array $categoryIds): Collection
    {
        return Brand::where('is_active', true)
            ->whereHas('products', fn ($query) => $query->whereIn('category_id', $categoryIds)->where('is_active', true))
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => BrandDto::fromModel($brand));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): BrandDto
    {
        $brand = Brand::create($attributes);

        return BrandDto::fromModel($brand);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): BrandDto
    {
        $brand = Brand::findOrFail($id);
        $brand->update($attributes);

        return BrandDto::fromModel($brand->fresh());
    }

    public function delete(int $id): void
    {
        Brand::findOrFail($id)->delete();
    }
}
