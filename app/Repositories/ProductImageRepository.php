<?php

namespace App\Repositories;

use App\Dto\Product\ProductImageDto;
use App\Models\ProductImage;
use Illuminate\Support\Collection;

class ProductImageRepository
{
    /**
     * @return Collection<int, ProductImageDto>
     */
    public function forProduct(int $productId): Collection
    {
        return ProductImage::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductImage $image) => ProductImageDto::fromModel($image));
    }

    public function find(int $imageId): ?ProductImageDto
    {
        $image = ProductImage::find($imageId);

        return $image ? ProductImageDto::fromModel($image) : null;
    }

    public function create(int $productId, string $path, int $sortOrder): ProductImageDto
    {
        $image = ProductImage::create([
            'product_id' => $productId,
            'path' => $path,
            'sort_order' => $sortOrder,
        ]);

        return ProductImageDto::fromModel($image);
    }

    /**
     * Where a newly-added image should sort — after every image the product
     * already has, so new uploads append instead of colliding at 0.
     */
    public function nextSortOrder(int $productId): int
    {
        return ProductImage::where('product_id', $productId)->max('sort_order') + 1;
    }

    public function delete(int $imageId): void
    {
        ProductImage::where('id', $imageId)->delete();
    }

    public function deleteForProduct(int $productId): void
    {
        ProductImage::where('product_id', $productId)->delete();
    }
}
