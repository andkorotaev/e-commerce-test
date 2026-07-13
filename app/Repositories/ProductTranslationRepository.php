<?php

namespace App\Repositories;

use App\Dto\Product\ProductTranslationDto;
use App\Models\ProductTranslation;

class ProductTranslationRepository
{
    public function upsert(int $productId, ProductTranslationDto $translation): ProductTranslation
    {
        return ProductTranslation::updateOrCreate(
            ['product_id' => $productId, 'locale' => $translation->locale],
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

    public function deleteForProduct(int $productId): void
    {
        ProductTranslation::where('product_id', $productId)->delete();
    }
}
