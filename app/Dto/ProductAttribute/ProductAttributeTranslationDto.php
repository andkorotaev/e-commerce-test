<?php

namespace App\Dto\ProductAttribute;

use App\Models\ProductAttributeTranslation;

final readonly class ProductAttributeTranslationDto
{
    public function __construct(
        public string $locale,
        public string $name,
    ) {}

    public static function fromModel(ProductAttributeTranslation $translation): self
    {
        return new self(
            locale: $translation->locale,
            name: $translation->name,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(string $locale, array $data): self
    {
        return new self(
            locale: $locale,
            name: $data['name'],
        );
    }
}
