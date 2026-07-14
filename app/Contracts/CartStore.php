<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * Common shape for both cart backends — CartService talks to whichever one
 * applies (DB for a logged-in user, cookie for a guest) through this
 * contract so it never has to branch on auth state itself beyond picking
 * which implementation to construct.
 */
interface CartStore
{
    /**
     * Raw stored lines, no product/variant data resolved yet — just
     * {product_id, variant_id, quantity} tuples for CartService to hydrate.
     *
     * @return Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>
     */
    public function rawLines(): Collection;

    /**
     * Adds $quantity more of a product(+variant) — increments if the line
     * already exists rather than overwriting it.
     */
    public function add(int $productId, ?int $variantId, int $quantity): void;

    /**
     * Sets a line's quantity outright (not additive) — used by the cart
     * page's quantity stepper/input.
     */
    public function updateQuantity(int $productId, ?int $variantId, int $quantity): void;

    public function remove(int $productId, ?int $variantId): void;

    public function clear(): void;
}
