<?php

namespace App\Http\Requests\Front\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'first_name' => "ім'я",
            'last_name' => 'прізвище',
            'email' => 'email',
            'phone' => 'телефон',
            'password' => 'пароль',
        ];
    }
}
