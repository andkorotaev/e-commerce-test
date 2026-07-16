<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $wishlist) {}

    /**
     * The heart-toggle buttons (product card + product page) submit this via
     * fetch so toggling doesn't reload the page — an ajax request gets back
     * just the new state as JSON to update the button in place; a plain
     * form submission (JS disabled) still gets the original redirect back.
     */
    public function toggle(Request $request, int $productId): RedirectResponse|JsonResponse
    {
        $isWishlisted = $this->wishlist->toggle($request->user()->id, $productId);

        if ($request->ajax()) {
            return response()->json(['isWishlisted' => $isWishlisted]);
        }

        return back();
    }
}
