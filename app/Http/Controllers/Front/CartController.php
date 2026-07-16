<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CartItemRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function show(Request $request): View
    {
        if ($request->ajax()) {
            return view('components.front.cart.contents', [
                'summary' => $this->cart->summary(),
            ]);
        }

        return view('front.cart.show', [
            'summary' => $this->cart->summary(),
        ]);
    }

    public function add(CartItemRequest $request): RedirectResponse|View
    {
        $this->cart->add($request->integer('product_id'), $request->variantId(), $request->integer('quantity', 1));

        $response = $this->respond($request);

        return $response instanceof RedirectResponse ? $response->with('status', 'added-to-cart') : $response;
    }

    public function update(CartItemRequest $request): RedirectResponse|View
    {
        $this->cart->updateQuantity($request->integer('product_id'), $request->variantId(), $request->integer('quantity', 1));

        return $this->respond($request);
    }

    public function remove(CartItemRequest $request): RedirectResponse|View
    {
        $this->cart->remove($request->integer('product_id'), $request->variantId());

        return $this->respond($request);
    }

    /**
     * The cart page's quantity/remove controls submit via fetch (so totals
     * recalculate without a full reload) — those requests get back just the
     * re-rendered list+summary partial. A plain form submission (JS
     * disabled, or "add to cart" from the product page) gets a normal
     * redirect back to wherever the request came from.
     */
    private function respond(Request $request): RedirectResponse|View
    {
        if ($request->ajax()) {
            return view('components.front.cart.contents', [
                'summary' => $this->cart->summary(),
            ]);
        }

        return back();
    }
}
