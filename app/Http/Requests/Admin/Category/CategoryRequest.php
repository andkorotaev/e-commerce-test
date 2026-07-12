<?php

namespace App\Http\Requests\Admin\Category;

use App\Dto\Category\CategoryInputDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDto(): CategoryInputDto
    {
        return CategoryInputDto::fromArray($this->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'parent_id' => ['nullable', 'exists:categories,id', $this->parentIsNotSelfOrDescendant()],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
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
        return Rule::unique('category_translations', 'slug')->where('locale', $locale);
    }

    protected function parentIsNotSelfOrDescendant(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            // Only meaningful when updating an existing category; a brand new
            // category can't yet be its own ancestor.
        };
    }
}
