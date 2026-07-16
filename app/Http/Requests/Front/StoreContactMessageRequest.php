<?php

namespace App\Http\Requests\Front;

use App\Dto\Contact\ContactMessageInputDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function getDto(): ContactMessageInputDto
    {
        return ContactMessageInputDto::fromArray($this->validated());
    }
}
