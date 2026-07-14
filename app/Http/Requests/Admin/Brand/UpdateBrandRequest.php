<?php

namespace App\Http\Requests\Admin\Brand;

use App\Models\Brand;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateBrandRequest extends BrandRequest
{
    protected ?Brand $brandModel = null;

    protected function brand(): Brand
    {
        return $this->brandModel ??= Brand::findOrFail($this->route('brandId'));
    }

    protected function uniqueSlugRule(): Unique
    {
        return Rule::unique('brands', 'slug')->ignore($this->brand()->id);
    }
}
