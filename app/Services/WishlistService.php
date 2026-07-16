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
        protected ReviewService $reviews,
    ) {}

    /**
     * @return Collection<int, ProductListItemDto>
     */
    public function forUser(int $userId): Collection
    {
        $products = $this->reviews->attachRatingsTo($this->wishlist->productsForUser($userId));

        return $products->map(fn (ProductListItemDto $product) => $product->withWishlisted(true));
    }

    /**
     * Bulk-flags which of $products are already saved by $userId — the
     * shared enrichment point for every product grid outside the wishlist
     * page itself (which is trivially all-wishlisted, see forUser() above).
     * A guest (null $userId) can't have anything wishlisted, so the list is
     * returned untouched rather than paying for a query.
     *
     * @param  Collection<int, ProductListItemDto>  $products
     * @return Collection<int, ProductListItemDto>
     */
    public function attachWishlistedTo(Collection $products, ?int $userId): Collection
    {
        if ($userId === null) {
            return $products;
        }

        $wishlistedIds = $this->wishlist->productIdsForUser($userId);

        return $products->map(fn (ProductListItemDto $product) => $product->withWishlisted($wishlistedIds->contains($product->id)));
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
