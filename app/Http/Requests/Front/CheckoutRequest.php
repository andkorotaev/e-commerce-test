<?php

namespace App\Http\Requests\Front;

use App\Dto\Order\OrderInputDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CheckoutRequest extends FormRequest
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
        $isGuest = $this->user() === null;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'email' => ['required', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],

            'delivery_carrier' => ['required', Rule::in(array_keys(config('shop.delivery_carriers')))],
            'delivery_type' => ['required', Rule::in(array_keys(config('shop.delivery_types')))],
            'delivery_point' => ['nullable', 'required_if:delivery_type,branch,postomat', 'string', 'max:50'],
            'payment_method' => ['required', Rule::in(array_keys(config('shop.payment_methods')))],

            'create_account' => ['sometimes', 'boolean'],
            'password' => [
                $isGuest && $this->boolean('create_account') ? 'required' : 'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function getDto(): OrderInputDto
    {
        return OrderInputDto::fromArray($this->validated());
    }
}
