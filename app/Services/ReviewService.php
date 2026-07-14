<?php

namespace App\Services;

use App\Dto\Review\ReviewDto;
use App\Dto\Review\ReviewInputDto;
use App\Repositories\ReviewRepository;
use Illuminate\Support\Collection;
use RuntimeException;
use Throwable;

class ReviewService
{
    public function __construct(
        protected ReviewRepository $reviews,
    ) {}

    /**
     * @return Collection<int, ReviewDto>
     */
    public function approvedForProduct(int $productId): Collection
    {
        return $this->reviews->approvedForProduct($productId);
    }

    /**
     * @return array{average: float, count: int}
     */
    public function ratingStats(int $productId): array
    {
        return $this->reviews->ratingStats($productId);
    }

    /**
     * @return Collection<int, ReviewDto>
     */
    public function all(): Collection
    {
        return $this->reviews->all();
    }

    /**
     * @throws RuntimeException
     */
    public function submit(ReviewInputDto $dto): ReviewDto
    {
        try {
            // Every submitted review starts unapproved regardless of input —
            // it only becomes public once an admin moderates it in.
            return $this->reviews->create([
                'product_id' => $dto->productId,
                'author_name' => $dto->authorName,
                'rating' => $dto->rating,
                'comment' => $dto->comment,
                'is_approved' => false,
            ]);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('Failed to submit review.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function approve(int $reviewId): void
    {
        try {
            $this->reviews->approve($reviewId);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to approve review #{$reviewId}.", previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $reviewId): void
    {
        try {
            $this->reviews->delete($reviewId);
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete review #{$reviewId}.", previous: $e);
        }
    }
}
