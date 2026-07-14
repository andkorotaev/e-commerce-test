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
