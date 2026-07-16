<?php

namespace App\Repositories;

use App\Dto\Product\ProductListItemDto;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Support\Collection;

class WishlistRepository
{
    /**
     * A user's saved products, newest first — the account cabinet's wishlist
     * page. Returns lean ProductListItemDto rows (the same shape the
     * category listing grid and product page's "similar products" rail
     * already use), not a full ProductDto.
     *
     * @return Collection<int, ProductListItemDto>
     */
    public function productsForUser(int $userId): Collection
    {
        $locale = app()->getLocale();

        $productIds = WishlistItem::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('product_id');

        $productsById = Product::whereIn('id', $productIds)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ])
            ->get()
            ->keyBy('id');

        return $productIds
            ->map(fn (int $id) => $productsById->get($id))
            ->filter()
            ->values()
            ->map(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    public function contains(int $userId, int $productId): bool
    {
        return WishlistItem::where('user_id', $userId)->where('product_id', $productId)->exists();
    }

    /**
     * @return Collection<int, int> every wishlisted product id for a user —
     *                               used by the product page to mark whether
     *                               the current product is already saved.
     */
    public function productIdsForUser(int $userId): Collection
    {
        return WishlistItem::where('user_id', $userId)->pluck('product_id');
    }

    public function add(int $userId, int $productId): void
    {
        WishlistItem::firstOrCreate(['user_id' => $userId, 'product_id' => $productId]);
    }

    public function remove(int $userId, int $productId): void
    {
        WishlistItem::where('user_id', $userId)->where('product_id', $productId)->delete();
    }

    public function countForUser(int $userId): int
    {
        return WishlistItem::where('user_id', $userId)->count();
    }
}
