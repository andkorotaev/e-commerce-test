<?php

namespace App\Http\Requests\Front;

use App\Dto\Review\ReviewInputDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * The author is always the authenticated user (this route sits behind
     * the `auth` middleware) — never a free-text field the client sends,
     * unlike the pre-accounts version of this form.
     */
    public function getDto(int $productId): ReviewInputDto
    {
        return ReviewInputDto::fromArray([
            ...$this->validated(),
            'product_id' => $productId,
            'user_id' => $this->user()->id,
            'author_name' => $this->user()->name,
        ]);
    }
}
