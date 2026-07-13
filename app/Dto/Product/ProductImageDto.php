<?php

namespace App\Dto\Product;

use App\Models\ProductImage;

final readonly class ProductImageDto
{
    public function __construct(
        public int $id,
        public string $path,
        public int $sortOrder,
    ) {}

    public static function fromModel(ProductImage $image): self
    {
        return new self(
            id: $image->id,
            path: $image->path,
            sortOrder: $image->sort_order,
        );
    }
}
