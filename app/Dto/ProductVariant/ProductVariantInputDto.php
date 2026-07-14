<?php

namespace App\Dto\ProductVariant;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class ProductVariantInputDto
{
    /**
     * @param  Collection<int, int>  $attributeValueIds
     */
    public function __construct(
        public ?int $id,
        public ?string $sku,
        public ?float $price,
        public int $stock,
        public ?UploadedFile $image,
        public bool $isActive,
        public Collection $attributeValueIds,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) && $data['id'] !== '' ? (int) $data['id'] : null,
            sku: $data['sku'] ?? null,
            price: isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null,
            stock: (int) ($data['stock'] ?? 0),
            image: $data['image'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
            attributeValueIds: collect($data['attribute_value_ids'] ?? [])->map(fn ($id) => (int) $id)->values(),
        );
    }
}
