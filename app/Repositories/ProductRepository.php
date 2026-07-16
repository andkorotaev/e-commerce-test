<?php

namespace App\Repositories;

use App\Dto\Product\ProductDto;
use App\Dto\Product\ProductFilterDto;
use App\Dto\Product\ProductListItemDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    /**
     * Every product, for the admin list.
     *
     * @return Collection<int, ProductDto>
     */
    protected const array WITH = ['translations', 'images', 'variants.attributeValues.translations'];

    public function all(): Collection
    {
        return Product::with(self::WITH)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Product $product) => ProductDto::fromModel($product));
    }

    public function find(int $id): ?ProductDto
    {
        $product = Product::with(self::WITH)->find($id);

        return $product ? ProductDto::fromModel($product) : null;
    }

    /**
     * Active products by id, as the lean listing-card shape, keyed by id —
     * the cart (and anywhere else that needs to hydrate a handful of
     * products by id at once, not a whole category) reuses this rather
     * than the full ProductDto.
     *
     * @param  array<int, int>  $ids
     * @return Collection<int, ProductListItemDto>
     */
    public function findManyAsListItems(array $ids): Collection
    {
        $locale = app()->getLocale();

        return Product::whereIn('id', $ids)
            ->where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ])
            ->get()
            ->map(fn (Product $product) => ProductListItemDto::fromModel($product, $locale))
            ->keyBy('id');
    }

    /**
     * A single product's own stock, without pulling in translations/images/
     * variants — used to clamp a cart line's quantity server-side.
     */
    public function stockFor(int $id): ?int
    {
        return Product::where('is_active', true)->whereKey($id)->value('stock');
    }

    /**
     * Active products in a given category — for storefront category pages.
     *
     * @return Collection<int, ProductDto>
     */
    public function activeForCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(self::WITH)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Product $product) => ProductDto::fromModel($product));
    }

    /**
     * A single active product by its translated slug — for the storefront
     * product page. Returns null if no active product has that slug in the
     * given locale.
     */
    public function findBySlug(string $slug, string $locale): ?ProductDto
    {
        $product = Product::where('is_active', true)
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)->where('locale', $locale);
            })
            ->with(self::WITH)
            ->first();

        return $product ? ProductDto::fromModel($product) : null;
    }

    /**
     * Active products in the same category as $excludeProductId, excluding
     * it — the product page's "similar products" rail.
     *
     * @return Collection<int, ProductListItemDto>
     */
    public function similarTo(int $categoryId, int $excludeProductId, int $limit): Collection
    {
        $locale = app()->getLocale();

        return Product::where('category_id', $categoryId)
            ->where('id', '!=', $excludeProductId)
            ->where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ])
            ->orderBy('sort_order')
            ->limit($limit)
            ->get()
            ->map(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    /**
     * Active products across a set of category ids (a category's own
     * subtree), filtered/sorted/searched per $filters — the storefront
     * category listing page. Returns lean ProductListItemDto rows (not the
     * full admin ProductDto) via the paginator's own ->through(), so the
     * DB-only-in/DTOs-out rule holds even under pagination.
     *
     * @param  array<int, int>  $categoryIds
     * @return LengthAwarePaginator<int, ProductListItemDto>
     */
    public function filtered(array $categoryIds, ProductFilterDto $filters): LengthAwarePaginator
    {
        $locale = app()->getLocale();

        $query = Product::query()
            ->select('products.*')
            ->join('product_translations', function ($join) use ($locale) {
                $join->on('product_translations.product_id', '=', 'products.id')
                    ->where('product_translations.locale', $locale);
            })
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', true)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ]);

        if ($filters->brandIds->isNotEmpty()) {
            $query->whereIn('products.brand_id', $filters->brandIds->all());
        }

        if ($filters->colorIds->isNotEmpty()) {
            $query->whereHas('variants', function ($query) use ($filters) {
                $query->where('is_active', true)
                    ->whereHas('attributeValues', fn ($query) => $query->whereIn('product_attribute_values.id', $filters->colorIds->all()));
            });
        }

        if ($filters->sizeIds->isNotEmpty()) {
            $query->whereHas('variants', function ($query) use ($filters) {
                $query->where('is_active', true)
                    ->whereHas('attributeValues', fn ($query) => $query->whereIn('product_attribute_values.id', $filters->sizeIds->all()));
            });
        }

        if ($filters->priceMin !== null) {
            $query->where('products.price', '>=', $filters->priceMin);
        }

        if ($filters->priceMax !== null) {
            $query->where('products.price', '<=', $filters->priceMax);
        }

        if ($filters->inStockOnly) {
            $query->where('products.stock', '>', 0);
        }

        if ($filters->search !== null && trim($filters->search) !== '') {
            $query->where('product_translations.name', 'like', '%'.$filters->search.'%');
        }

        match ($filters->sort) {
            'price_asc' => $query->orderBy('products.price'),
            'price_desc' => $query->orderByDesc('products.price'),
            'newest' => $query->orderByDesc('products.created_at'),
            'name' => $query->orderBy('product_translations.name'),
            default => $query->orderBy('products.sort_order'),
        };

        return $query->paginate($filters->perPage)
            ->withQueryString()
            ->through(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    /**
     * Min/max active price across a set of category ids — bounds for the
     * listing page's price-range filter inputs.
     *
     * @param  array<int, int>  $categoryIds
     * @return array{min: float, max: float}
     */
    public function priceRangeForCategories(array $categoryIds): array
    {
        $result = Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => $result?->min_price !== null ? (float) $result->min_price : 0.0,
            'max' => $result?->max_price !== null ? (float) $result->max_price : 0.0,
        ];
    }

    /**
     * Most recently added active products — the homepage's "New arrivals" rail.
     *
     * @return Collection<int, ProductListItemDto>
     */
    public function newArrivals(int $limit): Collection
    {
        $locale = app()->getLocale();

        return Product::where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    /**
     * Active products ordered by real customer approval — average approved
     * review rating first (best-rated first, products with no reviews yet
     * sort after those that have any), then review count as a tiebreaker,
     * then sort_order for products with no reviews at all. The homepage's
     * "Popular products" grid.
     *
     * @return Collection<int, ProductListItemDto>
     */
    public function popular(int $limit): Collection
    {
        $locale = app()->getLocale();

        $ids = Product::query()
            ->where('products.is_active', true)
            ->leftJoin('reviews', function ($join) {
                $join->on('reviews.product_id', '=', 'products.id')->where('reviews.is_approved', true);
            })
            ->groupBy('products.id')
            ->orderByRaw('AVG(reviews.rating) IS NULL')
            ->orderByDesc(DB::raw('AVG(reviews.rating)'))
            ->orderByDesc(DB::raw('COUNT(reviews.id)'))
            ->orderBy('products.sort_order')
            ->limit($limit)
            ->pluck('products.id');

        $products = Product::whereIn('id', $ids)
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'images' => fn ($query) => $query->orderBy('sort_order')->limit(1),
            ])
            ->get()
            ->keyBy('id');

        return $ids
            ->map(fn (int $id) => $products->get($id))
            ->filter()
            ->values()
            ->map(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    /**
     * Active products whose translated name matches $query, across the
     * whole catalog (not scoped to a category) — the header search modal's
     * results page.
     *
     * @return LengthAwarePaginator<int, ProductListItemDto>
     */
    public function search(string $query, int $perPage): LengthAwarePaginator
    {
        $locale = app()->getLocale();

        return Product::where('is_active', true)
            ->whereHas('translations', function ($q) use ($query, $locale) {
                $q->where('locale', $locale)->where('name', 'like', '%'.$query.'%');
            })
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale),
                'images' => fn ($q) => $q->orderBy('sort_order')->limit(1),
            ])
            ->orderBy('sort_order')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Product $product) => ProductListItemDto::fromModel($product, $locale));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ProductDto
    {
        $product = Product::create($attributes);

        return ProductDto::fromModel($product->load(self::WITH));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): ProductDto
    {
        $product = Product::findOrFail($id);
        $product->update($attributes);

        return ProductDto::fromModel($product->fresh(self::WITH));
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}
