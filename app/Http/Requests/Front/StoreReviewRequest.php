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
            'author_name' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ];
    }

    public function getDto(int $productId): ReviewInputDto
    {
        return ReviewInputDto::fromArray([...$this->validated(), 'product_id' => $productId]);
    }
}
