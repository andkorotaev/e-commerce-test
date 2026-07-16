<?php

namespace App\Repositories;

use App\Dto\Review\ReviewDto;
use App\Models\Review;
use Illuminate\Support\Collection;

class ReviewRepository
{
    /**
     * Approved reviews for a product, newest first — the storefront product
     * page's review list.
     *
     * @return Collection<int, ReviewDto>
     */
    public function approvedForProduct(int $productId): Collection
    {
        return Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Review $review) => ReviewDto::fromModel($review));
    }

    /**
     * Average rating + count over approved reviews only — an unmoderated
     * review shouldn't be able to skew the public-facing rating.
     *
     * @return array{average: float, count: int}
     */
    public function ratingStats(int $productId): array
    {
        $result = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as review_count')
            ->first();

        return [
            'average' => $result?->average_rating !== null ? round((float) $result->average_rating, 1) : 0.0,
            'count' => (int) ($result?->review_count ?? 0),
        ];
    }

    /**
     * Bulk version of ratingStats() — one query for a whole list of
     * products (e.g. the homepage's "Popular products" grid) instead of
     * one query per card.
     *
     * @param  array<int, int>  $productIds
     * @return array<int, array{average: float, count: int}> keyed by product id
     */
    public function ratingStatsForProducts(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        return Review::whereIn('product_id', $productIds)
            ->where('is_approved', true)
            ->selectRaw('product_id, AVG(rating) as average_rating, COUNT(*) as review_count')
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->product_id => [
                    'average' => round((float) $row->average_rating, 1),
                    'count' => (int) $row->review_count,
                ],
            ])
            ->all();
    }

    /**
     * Every review, newest first, with its product's name attached — the
     * admin moderation queue.
     *
     * @return Collection<int, ReviewDto>
     */
    public function all(): Collection
    {
        return Review::with('product.translations')
            ->orderBy('is_approved')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Review $review) => ReviewDto::fromModel($review));
    }

    public function find(int $id): ?ReviewDto
    {
        $review = Review::with('product.translations')->find($id);

        return $review ? ReviewDto::fromModel($review) : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ReviewDto
    {
        $review = Review::create($attributes);

        return ReviewDto::fromModel($review);
    }

    public function approve(int $id): void
    {
        Review::whereKey($id)->update(['is_approved' => true]);
    }

    public function delete(int $id): void
    {
        Review::whereKey($id)->delete();
    }
}
