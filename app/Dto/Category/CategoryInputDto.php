<?php

namespace App\Dto\Category;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

final readonly class CategoryInputDto
{
    /**
     * @param  Collection<int, CategoryTranslationDto>  $translations
     */
    public function __construct(
        public ?int $parentId,
        public ?UploadedFile $image,
        public bool $isActive,
        public int $sortOrder,
        public Collection $translations,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            parentId: $data['parent_id'] ?? null,
            image: $data['image'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
            sortOrder: (int) ($data['sort_order'] ?? 0),
            translations: collect($data['translations'])->map(
                fn (array $translation, string $locale) => CategoryTranslationDto::fromArray($locale, $translation)
            )->values(),
        );
    }
}
