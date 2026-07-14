<?php

namespace App\Dto\ProductAttribute;

use Illuminate\Support\Collection;

final readonly class ProductAttributeValueInputDto
{
    /**
     * @param  Collection<int, ProductAttributeValueTranslationDto>  $translations
     */
    public function __construct(
        public ?int $id,
        public string $slug,
        public Collection $translations,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) && $data['id'] !== '' ? (int) $data['id'] : null,
            slug: $data['slug'],
            translations: collect($data['translations'])->map(
                fn (array $translation, string $locale) => ProductAttributeValueTranslationDto::fromArray($locale, $translation)
            )->values(),
        );
    }
}
