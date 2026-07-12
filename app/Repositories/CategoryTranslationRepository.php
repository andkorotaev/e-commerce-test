<?php

namespace App\Repositories;

use App\Dto\Category\CategoryTranslationDto;
use App\Models\CategoryTranslation;

class CategoryTranslationRepository
{
    public function upsert(int $categoryId, CategoryTranslationDto $translation): CategoryTranslation
    {
        return CategoryTranslation::updateOrCreate(
            ['category_id' => $categoryId, 'locale' => $translation->locale],
            [
                'name' => $translation->name,
                'slug' => $translation->slug,
                'h1' => $translation->h1,
                'meta_title' => $translation->metaTitle,
                'meta_description' => $translation->metaDescription,
                'description' => $translation->description,
            ],
        );
    }

    public function deleteForCategory(int $categoryId): void
    {
        CategoryTranslation::where('category_id', $categoryId)->delete();
    }
}
