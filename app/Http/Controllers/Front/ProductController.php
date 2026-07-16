<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ProductAttributeService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $products,
        protected BrandService $brands,
        protected ProductAttributeService $attributes,
        protected ReviewService $reviews,
        protected CategoryService $categories,
        protected WishlistService $wishlist,
    ) {}

    public function show(Request $request, string $slug): View
    {
        $product = $this->products->findBySlug($slug, app()->getLocale());

        abort_if($product === null, 404);

        $brand = $product->brandId ? $this->brands->find($product->brandId) : null;
        $category = $this->categories->find($product->categoryId);
        $colorAttribute = $this->attributes->findBySlug('color');
        $sizeAttribute = $this->attributes->findBySlug('size');
        $user = $request->user();

        return view('front.products.show', [
            'product' => $product,
            'brand' => $brand,
            'category' => $category,
            'ancestors' => $category ? $this->categories->ancestors($category->id) : collect(),
            'colorAttributeId' => $colorAttribute?->id,
            'sizeAttributeId' => $sizeAttribute?->id,
            'similar' => $this->wishlist->attachWishlistedTo($this->products->similarTo($product), $user?->id),
            'ratingStats' => $this->reviews->ratingStats($product->id),
            'reviews' => $this->reviews->approvedForProduct($product->id),
            'isWishlisted' => $user ? $this->wishlist->productIdsForUser($user->id)->contains($product->id) : false,
        ]);
    }
}
