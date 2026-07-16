<?php

namespace App\Services;

use App\Contracts\CartStore;
use App\Dto\Cart\CartLineDto;
use App\Dto\Cart\CartSummaryDto;
use App\Dto\Product\ProductListItemDto;
use App\Dto\ProductVariant\ProductVariantDto;
use App\Models\User;
use App\Repositories\CartItemRepository;
use App\Repositories\GuestCartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private ?CartStore $store = null;

    public function __construct(
        protected ProductRepository $products,
        protected ProductVariantRepository $variants,
    ) {}

    /**
     * Adds $quantity of a product(+variant) to the current visitor's cart —
     * clamped to available stock, so a request for more than exists just
     * adds as much as it can rather than erroring.
     */
    public function add(int $productId, ?int $variantId, int $quantity): void
    {
        $stock = $this->stockFor($productId, $variantId);

        if ($stock === null || $stock < 1 || $quantity < 1) {
            return;
        }

        $this->store()->add($productId, $variantId, min($quantity, $stock));
    }

    /**
     * Sets a line's quantity outright — a quantity below 1 removes the line
     * entirely rather than leaving a zero-quantity row behind.
     */
    public function updateQuantity(int $productId, ?int $variantId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->store()->remove($productId, $variantId);

            return;
        }

        $stock = $this->stockFor($productId, $variantId);

        $this->store()->updateQuantity($productId, $variantId, $stock !== null ? min($quantity, $stock) : $quantity);
    }

    public function remove(int $productId, ?int $variantId): void
    {
        $this->store()->remove($productId, $variantId);
    }

    /**
     * Empties the current visitor's cart — called once an order is placed
     * for it.
     */
    public function clear(): void
    {
        $this->store()->clear();
    }

    public function summary(): CartSummaryDto
    {
        return CartSummaryDto::fromLines(
            $this->hydrate($this->store()->rawLines()),
            (float) config('shop.free_shipping_threshold'),
            (float) config('shop.flat_shipping_fee'),
        );
    }

    public function count(): int
    {
        return (int) $this->store()->rawLines()->sum('quantity');
    }

    /**
     * Called right after a guest successfully logs in or registers — folds
     * whatever they'd added to their cookie cart into their now-known
     * account's DB cart, so items added before authenticating aren't lost.
     * A no-op for a guest cart that's already empty.
     */
    public function mergeGuestCartIntoUserCart(User $user): void
    {
        $guestStore = new GuestCartRepository;
        $guestLines = $guestStore->rawLines();

        if ($guestLines->isEmpty()) {
            return;
        }

        $userStore = new CartItemRepository($user->id);

        foreach ($guestLines as $line) {
            $userStore->add($line['product_id'], $line['variant_id'], $line['quantity']);
        }

        $guestStore->clear();
    }

    private function store(): CartStore
    {
        return $this->store ??= Auth::check()
            ? new CartItemRepository(Auth::id())
            : new GuestCartRepository;
    }

    private function stockFor(int $productId, ?int $variantId): ?int
    {
        return $variantId ? $this->variants->stockFor($variantId) : $this->products->stockFor($productId);
    }

    /**
     * @param  Collection<int, array{product_id: int, variant_id: ?int, quantity: int}>  $rawLines
     * @return Collection<int, CartLineDto>
     */
    private function hydrate(Collection $rawLines): Collection
    {
        if ($rawLines->isEmpty()) {
            return collect();
        }

        $locale = app()->getLocale();

        $products = $this->products->findManyAsListItems($rawLines->pluck('product_id')->unique()->all());

        $variantIds = $rawLines->pluck('variant_id')->filter()->unique()->values()->all();
        $variants = $variantIds ? $this->variants->findMany($variantIds) : collect();

        return $rawLines
            ->map(function (array $line) use ($products, $variants, $locale) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    return null;
                }

                $variant = $line['variant_id'] ? $variants->get($line['variant_id']) : null;

                if ($line['variant_id'] && ! $variant) {
                    // The chosen variant was removed or deactivated since
                    // this line was added — drop it rather than show a
                    // cart line for a configuration that no longer exists.
                    return null;
                }

                return $this->buildLine($product, $variant, $line['quantity'], $locale);
            })
            ->filter()
            ->values();
    }

    private function buildLine(ProductListItemDto $product, ?ProductVariantDto $variant, int $quantity, string $locale): CartLineDto
    {
        $variantLabel = $variant?->attributeValues
            ->map(fn ($value) => $value->translation($locale)?->value ?? $value->slug)
            ->implode(' / ');

        return new CartLineDto(
            productId: $product->id,
            variantId: $variant?->id,
            name: $product->name,
            slug: $product->slug,
            image: $variant?->image ?? $product->image,
            variantLabel: $variantLabel !== '' ? $variantLabel : null,
            unitPrice: $variant?->price ?? $product->price,
            unitOldPrice: $product->oldPrice,
            quantity: $quantity,
            availableStock: $variant?->stock ?? $product->stock,
        );
    }
}
