<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\ReviewService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected CategoryService $categories,
        protected ProductService $products,
        protected ReviewService $reviews,
    ) {}

    public function index(): View
    {
        $popularProducts = $this->products->popular(8);
        $ratingStats = $this->reviews->ratingStatsForProducts($popularProducts->pluck('id')->all());

        $popularProducts = $popularProducts->map(
            fn ($product) => $product->withRating(
                $ratingStats[$product->id]['average'] ?? 0.0,
                $ratingStats[$product->id]['count'] ?? 0,
            )
        );

        return view('front.home.index', [
            'categories' => $this->categories->roots(),
            'newArrivals' => $this->products->newArrivals(10),
            'popularProducts' => $popularProducts,
        ]);
    }
}
