<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\ProductFilterRequest;
use App\Services\CategoryService;
use App\Services\ProductListingService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categories,
        protected ProductListingService $listing,
    ) {}

    public function show(string $slug, ProductFilterRequest $request): View
    {
        $category = $this->categories->findBySlug($slug, app()->getLocale());

        abort_if($category === null, 404);

        $listing = $this->listing->forCategory($category->id, $request->getFilterDto());

        return view('front.categories.show', [
            'category' => $category,
            'ancestors' => $this->categories->ancestors($category->id),
            'listing' => $listing,
            'filters' => $request->getFilterDto(),
        ]);
    }

    /**
     * AJAX endpoint: returns only the rendered results (grid + pagination),
     * so the JS filter/sort/search/pagination controls can swap it in
     * without a full page reload. Same underlying query as show() — just a
     * narrower view.
     */
    public function products(string $slug, ProductFilterRequest $request): View
    {
        $category = $this->categories->findBySlug($slug, app()->getLocale());

        abort_if($category === null, 404);

        $listing = $this->listing->forCategory($category->id, $request->getFilterDto());

        return view('components.front.products.listing.results', [
            'listing' => $listing,
        ]);
    }
}
