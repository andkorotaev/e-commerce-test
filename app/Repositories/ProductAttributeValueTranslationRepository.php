<?php

namespace App\Repositories;

use App\Dto\ProductAttribute\ProductAttributeValueTranslationDto;
use App\Models\ProductAttributeValueTranslation;

class ProductAttributeValueTranslationRepository
{
    public function upsert(int $valueId, ProductAttributeValueTranslationDto $translation): ProductAttributeValueTranslation
    {
        return ProductAttributeValueTranslation::updateOrCreate(
            ['product_attribute_value_id' => $valueId, 'locale' => $translation->locale],
            ['value' => $translation->value],
        );
    }
}
