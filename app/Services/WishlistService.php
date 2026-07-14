<?php

namespace App\Services;

use App\Dto\Product\ProductListItemDto;
use App\Repositories\WishlistRepository;
use Illuminate\Support\Collection;
use RuntimeException;
use Throwable;

class WishlistService
{
    public function __construct(
        protected WishlistRepository $wishlist,
    ) {}

    /**
     * @return Collection<int, ProductListItemDto>
     */
    public function forUser(int $userId): Collection
    {
        return $this->wishlist->productsForUser($userId);
    }

    /**
     * @return Collection<int, int>
     */
    public function productIdsForUser(int $userId): Collection
    {
        return $this->wishlist->productIdsForUser($userId);
    }

    public function countForUser(int $userId): int
    {
        return $this->wishlist->countForUser($userId);
    }

    /**
     * Adds the product if it isn't already saved, removes it if it is —
     * returns the new state (true = now saved) so the controller/view can
     * reflect it without a second query.
     *
     * @throws RuntimeException
     */
    public function toggle(int $userId, int $productId): bool
    {
        try {
            if ($this->wishlist->contains($userId, $productId)) {
                $this->wishlist->remove($userId, $productId);

                return false;
            }

            $this->wishlist->add($userId, $productId);

            return true;
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException('Failed to update wishlist.', previous: $e);
        }
    }
}
