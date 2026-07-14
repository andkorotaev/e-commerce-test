<?php

namespace App\Http\Requests\Admin\ProductAttribute;

use App\Dto\ProductAttribute\ProductAttributeInputDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class ProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDto(): ProductAttributeInputDto
    {
        return ProductAttributeInputDto::fromArray($this->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'slug' => ['required', 'string', 'max:255', $this->uniqueSlugRule()],
            'values' => ['array'],
            'values.*.id' => ['nullable', 'integer', 'exists:product_attribute_values,id'],
            // 'distinct' catches two rows in this submission reusing the same
            // slug — the DB's unique(attribute_id, slug) constraint can't be
            // consulted per-row here since the final row set is only known
            // once the whole request lands (rows are deleted-and-recreated).
            'values.*.slug' => ['required', 'string', 'max:255', 'distinct'],
        ];

        foreach (array_keys(config('localization.locales')) as $locale) {
            $rules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $rules["values.*.translations.{$locale}.value"] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    protected function uniqueSlugRule(): Unique
    {
        return Rule::unique('product_attributes', 'slug');
    }
}
