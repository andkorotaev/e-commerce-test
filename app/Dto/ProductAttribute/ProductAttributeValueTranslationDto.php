<?php

namespace App\Dto\ProductAttribute;

use App\Models\ProductAttributeValueTranslation;

final readonly class ProductAttributeValueTranslationDto
{
    public function __construct(
        public string $locale,
        public string $value,
    ) {}

    public static function fromModel(ProductAttributeValueTranslation $translation): self
    {
        return new self(
            locale: $translation->locale,
            value: $translation->value,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(string $locale, array $data): self
    {
        return new self(
            locale: $locale,
            value: $data['value'],
        );
    }
}
