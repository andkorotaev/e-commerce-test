<?php

namespace App\Repositories;

use App\Dto\ProductVariant\ProductVariantDto;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductVariantRepository
{
    protected const array WITH = ['attributeValues.translations'];

    /**
     * @return Collection<int, ProductVariantDto>
     */
    public function forProduct(int $productId): Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->with(self::WITH)
            ->get()
            ->map(fn (ProductVariant $variant) => ProductVariantDto::fromModel($variant));
    }

    public function find(int $id): ?ProductVariantDto
    {
        $variant = ProductVariant::with(self::WITH)->find($id);

        return $variant ? ProductVariantDto::fromModel($variant) : null;
    }

    /**
     * Active variants by id, keyed by id — the cart hydrates a handful of
     * variants at once rather than one at a time.
     *
     * @param  array<int, int>  $ids
     * @return Collection<int, ProductVariantDto>
     */
    public function findMany(array $ids): Collection
    {
        return ProductVariant::whereIn('id', $ids)
            ->where('is_active', true)
            ->with(self::WITH)
            ->get()
            ->map(fn (ProductVariant $variant) => ProductVariantDto::fromModel($variant))
            ->keyBy('id');
    }

    /**
     * A single variant's own stock — used to clamp a cart line's quantity
     * server-side.
     */
    public function stockFor(int $id): ?int
    {
        return ProductVariant::where('is_active', true)->whereKey($id)->value('stock');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, int>  $attributeValueIds
     */
    public function create(int $productId, array $attributes, array $attributeValueIds): ProductVariantDto
    {
        $variant = ProductVariant::create([...$attributes, 'product_id' => $productId]);
        $variant->attributeValues()->sync($attributeValueIds);

        return ProductVariantDto::fromModel($variant->load(self::WITH));
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, int>  $attributeValueIds
     */
    public function update(int $id, array $attributes, array $attributeValueIds): ProductVariantDto
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update($attributes);
        $variant->attributeValues()->sync($attributeValueIds);

        return ProductVariantDto::fromModel($variant->fresh(self::WITH));
    }

    public function delete(int $id): void
    {
        ProductVariant::where('id', $id)->delete();
    }

    /**
     * Delete every variant for a product except the given IDs — used when
     * saving a product's nested variants so removed rows actually disappear.
     *
     * @param  array<int, int>  $keepIds
     */
    public function deleteExcept(int $productId, array $keepIds): void
    {
        ProductVariant::where('product_id', $productId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }

    public function deleteForProduct(int $productId): void
    {
        ProductVariant::where('product_id', $productId)->delete();
    }
}
