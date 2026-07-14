<?php

namespace App\Dto\ProductAttribute;

use Illuminate\Support\Collection;

final readonly class ProductAttributeInputDto
{
    /**
     * @param  Collection<int, ProductAttributeTranslationDto>  $translations
     * @param  Collection<int, ProductAttributeValueInputDto>  $values
     */
    public function __construct(
        public string $slug,
        public Collection $translations,
        public Collection $values,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'],
            translations: collect($data['translations'])->map(
                fn (array $translation, string $locale) => ProductAttributeTranslationDto::fromArray($locale, $translation)
            )->values(),
            values: collect($data['values'] ?? [])->map(
                fn (array $value) => ProductAttributeValueInputDto::fromArray($value)
            )->values(),
        );
    }
}
