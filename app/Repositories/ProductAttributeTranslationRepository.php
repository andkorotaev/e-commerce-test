<?php

namespace App\Repositories;

use App\Dto\ProductAttribute\ProductAttributeTranslationDto;
use App\Models\ProductAttributeTranslation;

class ProductAttributeTranslationRepository
{
    public function upsert(int $attributeId, ProductAttributeTranslationDto $translation): ProductAttributeTranslation
    {
        return ProductAttributeTranslation::updateOrCreate(
            ['product_attribute_id' => $attributeId, 'locale' => $translation->locale],
            ['name' => $translation->name],
        );
    }

    public function deleteForAttribute(int $attributeId): void
    {
        ProductAttributeTranslation::where('product_attribute_id', $attributeId)->delete();
    }
}
