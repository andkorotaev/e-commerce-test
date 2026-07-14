<?php

namespace App\Http\Requests\Admin\Brand;

use App\Dto\Brand\BrandInputDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDto(): BrandInputDto
    {
        return BrandInputDto::fromArray($this->validated());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', $this->uniqueSlugRule()],
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['boolean'],
        ];
    }

    protected function uniqueSlugRule(): Unique
    {
        return Rule::unique('brands', 'slug');
    }
}
