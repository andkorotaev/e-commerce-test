<?php

namespace App\Http\Requests\Admin\ProductAttribute;

use App\Models\ProductAttribute;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateProductAttributeRequest extends ProductAttributeRequest
{
    protected ?ProductAttribute $attributeModel = null;

    protected function attribute(): ProductAttribute
    {
        return $this->attributeModel ??= ProductAttribute::findOrFail($this->route('productAttributeId'));
    }

    protected function uniqueSlugRule(): Unique
    {
        return Rule::unique('product_attributes', 'slug')->ignore($this->attribute()->id);
    }
}
