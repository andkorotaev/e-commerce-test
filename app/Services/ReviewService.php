<?php

namespace App\Services;

use App\Dto\Product\ProductListItemDto;
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
     * @param  array<int, int>  $productIds
     * @return array<int, array{average: float, count: int}>
     */
    public function ratingStatsForProducts(array $productIds): array
    {
        return $this->reviews->ratingStatsForProducts($productIds);
    }

    /**
     * Attaches real rating/review-count data to a list of product cards in
     * one bulk query — every storefront product grid (category listing,
     * new arrivals, popular, similar, wishlist) shows a rating per the
     * product card spec, so this is applied centrally here rather than
     * duplicated in every controller that builds one of those grids.
     *
     * @param  Collection<int, ProductListItemDto>  $products
     * @return Collection<int, ProductListItemDto>
     */
    public function attachRatingsTo(Collection $products): Collection
    {
        if ($products->isEmpty()) {
            return $products;
        }

        $stats = $this->ratingStatsForProducts($products->pluck('id')->all());

        return $products->map(fn (ProductListItemDto $product) => $product->withRating(
            $stats[$product->id]['average'] ?? 0.0,
            $stats[$product->id]['count'] ?? 0,
        ));
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
                'user_id' => $dto->userId,
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
