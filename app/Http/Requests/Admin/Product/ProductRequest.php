<?php

namespace App\Http\Requests\Admin\Product;

use App\Dto\Product\ProductInputDto;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Closure;
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
            'brand_id' => ['nullable', 'exists:brands,id'],
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
            'variants' => ['array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', $this->uniqueVariantSkuRule()],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['integer', 'min:0'],
            'variants.*.image' => ['nullable', 'image', 'max:4096'],
            'variants.*.is_active' => ['boolean'],
            'variants.*.attribute_value_ids' => ['required', 'array', 'min:1', $this->attributeValueIdsAreDistinctByAttribute()],
            'variants.*.attribute_value_ids.*' => ['integer', 'exists:product_attribute_values,id'],
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

    /**
     * Each variant row's own SKU uniqueness, ignoring itself on update. A
     * plain Rule::unique can't do this for a wildcarded field since every row
     * needs a *different* ignore id — resolved via the row's own submitted
     * "id" instead of a single ignore() call.
     */
    protected function uniqueVariantSkuRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            preg_match('/variants\.(\d+)\.sku/', $attribute, $matches);
            $variantId = $this->input("variants.{$matches[1]}.id");

            $exists = ProductVariant::where('sku', $value)
                ->when($variantId, fn ($query) => $query->where('id', '!=', $variantId))
                ->exists();

            if ($exists) {
                $fail(__('This SKU is already in use by another variant.'));
            }
        };
    }

    /**
     * The DB's unique(variant_id, attribute_value_id) constraint only stops
     * the exact same value twice — it can't stop a variant picking both
     * "Red" and "Blue" (two different values of the same Color attribute).
     * That's only checkable here, once we know which attribute each
     * submitted value belongs to.
     */
    protected function attributeValueIdsAreDistinctByAttribute(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_array($value)) {
                return;
            }

            $attributeIds = ProductAttributeValue::whereIn('id', $value)
                ->pluck('product_attribute_id', 'id');

            $seen = [];
            foreach ($value as $valueId) {
                $attributeId = $attributeIds->get($valueId);

                if ($attributeId === null) {
                    continue;
                }

                if (in_array($attributeId, $seen, true)) {
                    $fail(__('A variant cannot have two values for the same attribute.'));

                    return;
                }

                $seen[] = $attributeId;
            }
        };
    }
}
