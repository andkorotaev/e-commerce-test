<?php

namespace App\Dto\Product;

use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Lean card shape for the category listing grid — deliberately not the full
 * admin-facing ProductDto (no full image/variant collections, no meta
 * fields), the same "split only when the shape genuinely differs" rule
 * applied elsewhere in this codebase. This is the ONE product card shape
 * used everywhere a product grid appears (category listing, new arrivals,
 * popular products, similar products, wishlist) — every such card shows the
 * same fields (photo/name/price/rating/short description/add-to-cart), so
 * they all share this single DTO and a single Blade card component rather
 * than each place inventing its own slightly-different shape.
 */
final readonly class ProductListItemDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $slug,
        public float $price,
        public ?float $oldPrice,
        public ?string $image,
        public ?string $description,
        public int $stock,
        public ?float $rating = null,
        public ?int $reviewsCount = null,
        public bool $isWishlisted = false,
    ) {}

    public static function fromModel(Product $product, string $locale): self
    {
        $translation = $product->translations->firstWhere('locale', $locale);

        return new self(
            id: $product->id,
            name: $translation?->name ?? '',
            slug: $translation?->slug,
            price: (float) $product->price,
            oldPrice: $product->old_price !== null ? (float) $product->old_price : null,
            image: $product->images->first()?->path,
            description: $translation?->description ? Str::limit($translation->description, 90) : null,
            stock: $product->stock,
        );
    }

    /**
     * Returns a copy with rating data attached — kept separate from
     * fromModel() since rating stats come from a bulk query across a whole
     * product list, not from the Product model itself (mirrors
     * CategoryDto::withChildren()'s "augment after the fact" shape).
     */
    public function withRating(float $rating, int $reviewsCount): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            price: $this->price,
            oldPrice: $this->oldPrice,
            image: $this->image,
            description: $this->description,
            stock: $this->stock,
            rating: $rating,
            reviewsCount: $reviewsCount,
            isWishlisted: $this->isWishlisted,
        );
    }

    /**
     * Returns a copy flagged as wishlisted (or not) — kept separate from
     * fromModel() for the same reason as withRating(): whether a product is
     * in the current user's wishlist comes from a separate bulk lookup
     * against wishlist_items, not from the Product model itself.
     */
    public function withWishlisted(bool $isWishlisted): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            price: $this->price,
            oldPrice: $this->oldPrice,
            image: $this->image,
            description: $this->description,
            stock: $this->stock,
            rating: $this->rating,
            reviewsCount: $this->reviewsCount,
            isWishlisted: $isWishlisted,
        );
    }
}
