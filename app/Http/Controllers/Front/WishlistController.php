<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $wishlist) {}

    public function toggle(Request $request, int $productId): RedirectResponse
    {
        $this->wishlist->toggle($request->user()->id, $productId);

        return back();
    }
}
