<?php

namespace App\Dto\Review;

final readonly class ReviewInputDto
{
    public function __construct(
        public int $productId,
        public int $userId,
        public string $authorName,
        public int $rating,
        public string $comment,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            userId: (int) $data['user_id'],
            authorName: $data['author_name'],
            rating: (int) $data['rating'],
            comment: $data['comment'],
        );
    }
}
