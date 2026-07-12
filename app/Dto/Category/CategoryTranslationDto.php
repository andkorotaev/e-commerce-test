<?php

namespace App\Dto\Category;

use App\Models\CategoryTranslation;

final readonly class CategoryTranslationDto
{
    public function __construct(
        public string $locale,
        public string $name,
        public string $slug,
        public ?string $h1,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $description,
    ) {}

    public static function fromModel(CategoryTranslation $translation): self
    {
        return new self(
            locale: $translation->locale,
            name: $translation->name,
            slug: $translation->slug,
            h1: $translation->h1,
            metaTitle: $translation->meta_title,
            metaDescription: $translation->meta_description,
            description: $translation->description,
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
            slug: $data['slug'],
            h1: $data['h1'] ?? null,
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
