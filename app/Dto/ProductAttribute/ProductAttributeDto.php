<?php

namespace App\Dto\ProductAttribute;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeTranslation;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Collection;

final readonly class ProductAttributeDto
{
    /**
     * @param  Collection<int, ProductAttributeTranslationDto>  $translations
     * @param  Collection<int, ProductAttributeValueDto>  $values
     */
    public function __construct(
        public int $id,
        public string $slug,
        public Collection $translations,
        public Collection $values,
    ) {}

    public static function fromModel(ProductAttribute $attribute): self
    {
        return new self(
            id: $attribute->id,
            slug: $attribute->slug,
            translations: $attribute->translations->map(
                fn (ProductAttributeTranslation $translation) => ProductAttributeTranslationDto::fromModel($translation)
            ),
            values: $attribute->values->map(
                fn (ProductAttributeValue $value) => ProductAttributeValueDto::fromModel($value)
            ),
        );
    }

    public function translation(string $locale): ?ProductAttributeTranslationDto
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
