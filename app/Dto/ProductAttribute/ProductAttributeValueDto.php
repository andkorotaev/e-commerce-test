<?php

namespace App\Dto\ProductAttribute;

use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Support\Collection;

final readonly class ProductAttributeValueDto
{
    /**
     * @param  Collection<int, ProductAttributeValueTranslationDto>  $translations
     */
    public function __construct(
        public int $id,
        public int $productAttributeId,
        public string $slug,
        public Collection $translations,
    ) {}

    public static function fromModel(ProductAttributeValue $value): self
    {
        return new self(
            id: $value->id,
            productAttributeId: $value->product_attribute_id,
            slug: $value->slug,
            translations: $value->translations->map(
                fn (ProductAttributeValueTranslation $translation) => ProductAttributeValueTranslationDto::fromModel($translation)
            ),
        );
    }

    public function translation(string $locale): ?ProductAttributeValueTranslationDto
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
