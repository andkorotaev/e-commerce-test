<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreReviewRequest;
use App\Services\ProductService;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(
        protected ProductService $products,
        protected ReviewService $reviews,
    ) {}

    public function store(StoreReviewRequest $request, string $slug): RedirectResponse
    {
        $product = $this->products->findBySlug($slug, app()->getLocale());

        abort_if($product === null, 404);

        $this->reviews->submit($request->getDto($product->id));

        return back()->with('status', 'review-submitted');
    }
}
