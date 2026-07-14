<?php

namespace App\Http\Requests\Front;

use App\Dto\Product\ProductFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category' => ['sometimes', 'array'],
            'category.*' => ['integer', 'exists:categories,id'],
            'brand' => ['sometimes', 'array'],
            'brand.*' => ['integer', 'exists:brands,id'],
            'color' => ['sometimes', 'array'],
            'color.*' => ['integer', 'exists:product_attribute_values,id'],
            'size' => ['sometimes', 'array'],
            'size.*' => ['integer', 'exists:product_attribute_values,id'],
            'price_min' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_max' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'in_stock' => ['sometimes'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort' => ['sometimes', 'in:popularity,newest,price_asc,price_desc,name'],
        ];
    }

    public function getFilterDto(): ProductFilterDto
    {
        return ProductFilterDto::fromArray($this->validated());
    }
}
