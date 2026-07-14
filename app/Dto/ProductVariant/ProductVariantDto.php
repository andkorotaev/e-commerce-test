<?php

namespace App\Dto\ProductVariant;

use App\Dto\ProductAttribute\ProductAttributeValueDto;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

final readonly class ProductVariantDto
{
    /**
     * @param  Collection<int, ProductAttributeValueDto>  $attributeValues
     */
    public function __construct(
        public int $id,
        public int $productId,
        public ?string $sku,
        public ?float $price,
        public int $stock,
        public ?string $image,
        public bool $isActive,
        public Collection $attributeValues,
    ) {}

    public static function fromModel(ProductVariant $variant): self
    {
        return new self(
            id: $variant->id,
            productId: $variant->product_id,
            sku: $variant->sku,
            price: $variant->price !== null ? (float) $variant->price : null,
            stock: $variant->stock,
            image: $variant->image,
            isActive: $variant->is_active,
            attributeValues: $variant->attributeValues->map(
                fn (ProductAttributeValue $value) => ProductAttributeValueDto::fromModel($value)
            ),
        );
    }
}
