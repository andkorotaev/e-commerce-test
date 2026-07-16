<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected CategoryService $categories,
        protected ProductService $products,
        protected WishlistService $wishlist,
    ) {}

    public function index(Request $request): View
    {
        $userId = $request->user()?->id;

        return view('front.home.index', [
            'categories' => $this->categories->roots(),
            'newArrivals' => $this->wishlist->attachWishlistedTo($this->products->newArrivals(10), $userId),
            'popularProducts' => $this->wishlist->attachWishlistedTo($this->products->popular(8), $userId),
        ]);
    }
}
