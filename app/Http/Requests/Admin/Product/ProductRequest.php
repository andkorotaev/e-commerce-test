<?php

namespace App\Http\Requests\Admin\Product;

use App\Dto\Product\ProductInputDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDto(): ProductInputDto
    {
        return ProductInputDto::fromArray($this->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:100', $this->uniqueSkuRule()],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'images' => ['array'],
            'images.*' => ['image', 'max:4096'],
            'delete_images' => ['array'],
            'delete_images.*' => ['integer'],
        ];

        foreach (array_keys(config('localization.locales')) as $locale) {
            $rules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$locale}.slug"] = ['required', 'string', 'max:255', $this->uniqueSlugRule($locale)];
            $rules["translations.{$locale}.h1"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.meta_title"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$locale}.meta_description"] = ['nullable', 'string'];
            $rules["translations.{$locale}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    protected function uniqueSlugRule(string $locale): Unique
    {
        return Rule::unique('product_translations', 'slug')->where('locale', $locale);
    }

    protected function uniqueSkuRule(): Unique
    {
        return Rule::unique('products', 'sku');
    }
}
