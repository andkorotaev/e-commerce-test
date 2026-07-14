<?php

namespace App\Dto\Review;

use App\Models\Review;
use Carbon\Carbon;

final readonly class ReviewDto
{
    public function __construct(
        public int $id,
        public int $productId,
        public string $authorName,
        public int $rating,
        public string $comment,
        public bool $isApproved,
        public Carbon $createdAt,
        public ?string $productName = null,
    ) {}

    public static function fromModel(Review $review): self
    {
        return new self(
            id: $review->id,
            productId: $review->product_id,
            authorName: $review->author_name,
            rating: $review->rating,
            comment: $review->comment,
            isApproved: $review->is_approved,
            createdAt: $review->created_at,
            productName: $review->relationLoaded('product')
                ? $review->product?->translations->firstWhere('locale', 'uk')?->name
                : null,
        );
    }
}
