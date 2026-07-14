<?php

namespace App\Repositories;

use App\Contracts\CartStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

/**
 * Cookie-backed cart for a guest (not logged in) — an encrypted cookie
 * holding a JSON array of {product_id, variant_id, quantity} tuples.
 * Laravel encrypts/decrypts every cookie by default (EncryptCookies
 * middleware), so this never has to handle that itself.
 *
 * Keeps an in-memory copy after the first read so that an add/update/remove
 * followed by another read in the SAME request sees the change immediately —
 * Cookie::queue() only affects the outgoing response, not the request
 * object already in hand, so without this a "read right after write" within
 * one request would see stale (pre-write) data.
 */
class GuestCartRepository implements CartStore
{
    private const COOKIE_NAME = 'guest_cart';

    /**
     * @var Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>|null
     */
    private ?Collection $lines = null;

    /**
     * @return Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>
     */
    public function rawLines(): Collection
    {
        if ($this->lines !== null) {
            return $this->lines;
        }

        $raw = request()->cookie(self::COOKIE_NAME);
        $decoded = $raw ? (json_decode($raw, true) ?: []) : [];

        return $this->lines = collect($decoded)->map(fn (array $line) => [
            'product_id' => (int) $line['product_id'],
            'variant_id' => isset($line['variant_id']) && $line['variant_id'] !== null ? (int) $line['variant_id'] : null,
            'quantity' => (int) $line['quantity'],
        ])->values();
    }

    public function add(int $productId, ?int $variantId, int $quantity): void
    {
        $lines = $this->rawLines();
        $index = $this->indexOf($lines, $productId, $variantId);

        if ($index === null) {
            $this->lines = $lines->push(['product_id' => $productId, 'variant_id' => $variantId, 'quantity' => $quantity]);
        } else {
            $lines[$index] = [...$lines[$index], 'quantity' => $lines[$index]['quantity'] + $quantity];
            $this->lines = $lines;
        }

        $this->persist();
    }

    public function updateQuantity(int $productId, ?int $variantId, int $quantity): void
    {
        $lines = $this->rawLines();
        $index = $this->indexOf($lines, $productId, $variantId);

        if ($index === null) {
            return;
        }

        $lines[$index] = [...$lines[$index], 'quantity' => $quantity];
        $this->lines = $lines;

        $this->persist();
    }

    public function remove(int $productId, ?int $variantId): void
    {
        $this->lines = $this->rawLines()
            ->reject(fn (array $line) => $line['product_id'] === $productId && $line['variant_id'] === $variantId)
            ->values();

        $this->persist();
    }

    public function clear(): void
    {
        $this->lines = collect();
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }

    /**
     * @param  Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>  $lines
     */
    private function indexOf(Collection $lines, int $productId, ?int $variantId): ?int
    {
        foreach ($lines as $index => $line) {
            if ($line['product_id'] === $productId && $line['variant_id'] === $variantId) {
                return $index;
            }
        }

        return null;
    }

    private function persist(): void
    {
        Cookie::queue(
            self::COOKIE_NAME,
            json_encode($this->lines->values()->all()),
            (int) config('shop.guest_cart_cookie_minutes'),
        );
    }
}
