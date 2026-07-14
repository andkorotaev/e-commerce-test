<?php

namespace App\Dto\Product;

use App\Dto\ProductVariant\ProductVariantInputDto;
use Illuminate\Support\Collection;

final readonly class ProductInputDto
{
    /**
     * @param  Collection<int, ProductTranslationDto>  $translations
     * @param  Collection<int, \Illuminate\Http\UploadedFile>  $newImages
     * @param  Collection<int, int>  $deleteImageIds
     * @param  Collection<int, ProductVariantInputDto>  $variants
     */
    public function __construct(
        public int $categoryId,
        public ?int $brandId,
        public ?string $sku,
        public float $price,
        public ?float $oldPrice,
        public int $stock,
        public bool $isActive,
        public int $sortOrder,
        public Collection $translations,
        public Collection $newImages,
        public Collection $deleteImageIds,
        public Collection $variants,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: (int) $data['category_id'],
            brandId: isset($data['brand_id']) && $data['brand_id'] !== '' ? (int) $data['brand_id'] : null,
            sku: $data['sku'] ?? null,
            price: (float) $data['price'],
            oldPrice: isset($data['old_price']) && $data['old_price'] !== '' ? (float) $data['old_price'] : null,
            stock: (int) ($data['stock'] ?? 0),
            isActive: (bool) ($data['is_active'] ?? false),
            sortOrder: (int) ($data['sort_order'] ?? 0),
            translations: collect($data['translations'])->map(
                fn (array $translation, string $locale) => ProductTranslationDto::fromArray($locale, $translation)
            )->values(),
            newImages: collect($data['images'] ?? [])->filter(),
            deleteImageIds: collect($data['delete_images'] ?? [])->map(fn ($id) => (int) $id),
            variants: collect($data['variants'] ?? [])->map(
                fn (array $variant) => ProductVariantInputDto::fromArray($variant)
            )->values(),
        );
    }
}
