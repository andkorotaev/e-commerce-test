<?php

namespace App\Dto\Product;

use App\Dto\ProductVariant\ProductVariantDto;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTranslation;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

final readonly class ProductDto
{
    /**
     * @param  Collection<int, ProductTranslationDto>  $translations
     * @param  Collection<int, ProductImageDto>  $images
     * @param  Collection<int, ProductVariantDto>  $variants
     */
    public function __construct(
        public int $id,
        public int $categoryId,
        public ?int $brandId,
        public ?string $sku,
        public float $price,
        public ?float $oldPrice,
        public int $stock,
        public bool $isActive,
        public int $sortOrder,
        public Collection $translations,
        public Collection $images,
        public Collection $variants,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            categoryId: $product->category_id,
            brandId: $product->brand_id,
            sku: $product->sku,
            price: (float) $product->price,
            oldPrice: $product->old_price !== null ? (float) $product->old_price : null,
            stock: $product->stock,
            isActive: $product->is_active,
            sortOrder: $product->sort_order,
            translations: $product->translations->map(
                fn (ProductTranslation $translation) => ProductTranslationDto::fromModel($translation)
            ),
            images: $product->images->map(
                fn (ProductImage $image) => ProductImageDto::fromModel($image)
            ),
            variants: $product->variants->map(
                fn (ProductVariant $variant) => ProductVariantDto::fromModel($variant)
            ),
        );
    }

    public function translation(string $locale): ?ProductTranslationDto
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function primaryImage(): ?ProductImageDto
    {
        return $this->images->first();
    }
}
