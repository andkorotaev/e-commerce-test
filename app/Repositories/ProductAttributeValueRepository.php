<?php

namespace App\Repositories;

use App\Dto\ProductAttribute\ProductAttributeValueDto;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Collection;

class ProductAttributeValueRepository
{
    /**
     * @return Collection<int, ProductAttributeValueDto>
     */
    public function forAttribute(int $attributeId): Collection
    {
        return ProductAttributeValue::where('product_attribute_id', $attributeId)
            ->with('translations')
            ->get()
            ->map(fn (ProductAttributeValue $value) => ProductAttributeValueDto::fromModel($value));
    }

    public function find(int $id): ?ProductAttributeValueDto
    {
        $value = ProductAttributeValue::with('translations')->find($id);

        return $value ? ProductAttributeValueDto::fromModel($value) : null;
    }

    /**
     * Values of a given attribute (by slug, e.g. "color"/"size") actually
     * used by an active variant of an active product within the given
     * category ids — the storefront listing page's color/size filter facets.
     *
     * @param  array<int, int>  $categoryIds
     * @return Collection<int, ProductAttributeValueDto>
     */
    public function facetForCategories(string $attributeSlug, array $categoryIds): Collection
    {
        return ProductAttributeValue::query()
            ->whereHas('attribute', fn ($query) => $query->where('slug', $attributeSlug))
            ->whereHas('variants', function ($query) use ($categoryIds) {
                $query->where('is_active', true)
                    ->whereHas('product', fn ($query) => $query->whereIn('category_id', $categoryIds)->where('is_active', true));
            })
            ->with('translations')
            ->get()
            ->map(fn (ProductAttributeValue $value) => ProductAttributeValueDto::fromModel($value));
    }

    public function create(int $attributeId, string $slug): ProductAttributeValueDto
    {
        $value = ProductAttributeValue::create([
            'product_attribute_id' => $attributeId,
            'slug' => $slug,
        ]);

        return ProductAttributeValueDto::fromModel($value->load('translations'));
    }

    public function update(int $id, string $slug): ProductAttributeValueDto
    {
        $value = ProductAttributeValue::findOrFail($id);
        $value->update(['slug' => $slug]);

        return ProductAttributeValueDto::fromModel($value->fresh('translations'));
    }

    public function delete(int $id): void
    {
        ProductAttributeValue::where('id', $id)->delete();
    }

    /**
     * Delete every value for an attribute except the given IDs — used when
     * saving an attribute's nested values so removed rows actually disappear.
     *
     * @param  array<int, int>  $keepIds
     */
    public function deleteExcept(int $attributeId, array $keepIds): void
    {
        ProductAttributeValue::where('product_attribute_id', $attributeId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }
}
