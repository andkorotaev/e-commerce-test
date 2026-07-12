<?php

namespace App\Dto\Category;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Support\Collection;

final readonly class CategoryDto
{
    /**
     * @param  Collection<int, CategoryTranslationDto>  $translations
     * @param  Collection<int, CategoryDto>  $children
     */
    public function __construct(
        public int $id,
        public ?int $parentId,
        public ?string $image,
        public bool $isActive,
        public int $sortOrder,
        public Collection $translations,
        public Collection $children,
    ) {}

    public static function fromModel(Category $category): self
    {
        return new self(
            id: $category->id,
            parentId: $category->parent_id,
            image: $category->image,
            isActive: $category->is_active,
            sortOrder: $category->sort_order,
            translations: $category->translations->map(
                fn (CategoryTranslation $translation) => CategoryTranslationDto::fromModel($translation)
            ),
            children: collect(),
        );
    }

    public function translation(string $locale): ?CategoryTranslationDto
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    /**
     * @param  Collection<int, CategoryDto>  $children
     */
    public function withChildren(Collection $children): self
    {
        return new self(
            id: $this->id,
            parentId: $this->parentId,
            image: $this->image,
            isActive: $this->isActive,
            sortOrder: $this->sortOrder,
            translations: $this->translations,
            children: $children,
        );
    }
}
