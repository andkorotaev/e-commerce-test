<?php

namespace App\Dto\Product;

use App\Dto\Brand\BrandDto;
use App\Dto\ProductAttribute\ProductAttributeValueDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Output DTO for the category product listing: the paginated result plus
 * the facet option lists (brands/colors/sizes actually present among
 * products in the current category scope) and the scope's price bounds —
 * a genuinely different shape from a single ProductDto, so it gets its own
 * class rather than being bolted onto an existing one.
 */
final readonly class ProductListingResultDto
{
    /**
     * @param  LengthAwarePaginator<int, ProductListItemDto>  $products
     * @param  Collection<int, BrandDto>  $brands
     * @param  Collection<int, ProductAttributeValueDto>  $colors
     * @param  Collection<int, ProductAttributeValueDto>  $sizes
     */
    public function __construct(
        public LengthAwarePaginator $products,
        public Collection $brands,
        public Collection $colors,
        public Collection $sizes,
        public float $priceMin,
        public float $priceMax,
    ) {}
}
