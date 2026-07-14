<?php

namespace App\Repositories;

use App\Contracts\CartStore;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * DB-backed cart for a logged-in user. Constructed per-user (not a
 * singleton) since it's always scoped to one user_id for the duration of a
 * request.
 */
class CartItemRepository implements CartStore
{
    public function __construct(
        protected int $userId,
    ) {}

    /**
     * @return Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>
     */
    public function rawLines(): Collection
    {
        return CartItem::where('user_id', $this->userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CartItem $item) => [
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
            ]);
    }

    public function add(int $productId, ?int $variantId, int $quantity): void
    {
        $existing = $this->find($productId, $variantId);

        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + $quantity]);

            return;
        }

        CartItem::create([
            'user_id' => $this->userId,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(int $productId, ?int $variantId, int $quantity): void
    {
        $this->find($productId, $variantId)?->update(['quantity' => $quantity]);
    }

    public function remove(int $productId, ?int $variantId): void
    {
        $this->baseQuery($productId, $variantId)->delete();
    }

    public function clear(): void
    {
        CartItem::where('user_id', $this->userId)->delete();
    }

    private function find(int $productId, ?int $variantId): ?CartItem
    {
        return $this->baseQuery($productId, $variantId)->first();
    }

    private function baseQuery(int $productId, ?int $variantId): Builder
    {
        $query = CartItem::where('user_id', $this->userId)->where('product_id', $productId);

        return $variantId === null
            ? $query->whereNull('product_variant_id')
            : $query->where('product_variant_id', $variantId);
    }
}
