<?php

namespace App\Services;

use App\Dto\Product\ProductFilterDto;
use App\Dto\Product\ProductListingResultDto;
use App\Repositories\BrandRepository;
use App\Repositories\ProductAttributeValueRepository;
use App\Repositories\ProductRepository;

class ProductListingService
{
    public function __construct(
        protected ProductRepository $products,
        protected BrandRepository $brands,
        protected ProductAttributeValueRepository $attributeValues,
        protected CategoryService $categories,
        protected ReviewService $reviews,
        protected WishlistService $wishlist,
    ) {}

    /**
     * Builds the filtered/sorted/paginated product listing plus filter
     * facets for a category page. The default scope is the category's own
     * subtree (itself + every descendant); if the caller narrowed to
     * specific child categories via the "category" filter checkboxes, scope
     * to the union of those children's own subtrees instead.
     */
    public function forCategory(int $categoryId, ProductFilterDto $filters, ?int $userId = null): ProductListingResultDto
    {
        $categoryIds = $filters->categoryIds->isNotEmpty()
            ? $filters->categoryIds->flatMap(fn (int $id) => $this->categories->descendantIds($id))->unique()->values()->all()
            : $this->categories->descendantIds($categoryId);

        $priceRange = $this->products->priceRangeForCategories($categoryIds);

        $products = $this->products->filtered($categoryIds, $filters);
        $products->setCollection($this->wishlist->attachWishlistedTo($this->reviews->attachRatingsTo($products->getCollection()), $userId));

        return new ProductListingResultDto(
            products: $products,
            brands: $this->brands->facetForCategories($categoryIds),
            colors: $this->attributeValues->facetForCategories('color', $categoryIds),
            sizes: $this->attributeValues->facetForCategories('size', $categoryIds),
            priceMin: $priceRange['min'],
            priceMax: $priceRange['max'],
        );
    }
}
