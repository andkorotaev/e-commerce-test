<?php

namespace App\Dto\Product;

use App\Models\Product;

/**
 * Lean card shape for the category listing grid — deliberately not the full
 * admin-facing ProductDto (no full image/variant collections, no meta
 * fields), the same "split only when the shape genuinely differs" rule
 * applied elsewhere in this codebase.
 */
final readonly class ProductListItemDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $slug,
        public float $price,
        public ?float $oldPrice,
        public ?string $image,
        public ?string $brandName,
        public int $stock,
    ) {}

    public static function fromModel(Product $product, string $locale): self
    {
        $translation = $product->translations->firstWhere('locale', $locale);

        return new self(
            id: $product->id,
            name: $translation?->name ?? '',
            slug: $translation?->slug,
            price: (float) $product->price,
            oldPrice: $product->old_price !== null ? (float) $product->old_price : null,
            image: $product->images->first()?->path,
            brandName: $product->brand?->name,
            stock: $product->stock,
        );
    }
}
