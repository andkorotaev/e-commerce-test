<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orders,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $summary = $this->cart->summary();

        if ($summary->lines->isEmpty()) {
            return redirect()->route('front.cart.show');
        }

        $user = $request->user();
        $nameParts = $user ? explode(' ', $user->name, 2) : ['', ''];

        return view('front.checkout.show', [
            'summary' => $summary,
            'user' => $user,
            'prefill' => [
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user?->email ?? '',
            ],
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->orders->checkout($request->getDto(), $request->user());

        session()->put('last_guest_order_id', $order->id);

        return redirect()->route('front.checkout.thank-you', $order->id);
    }

    public function thankYou(Request $request, int $order): View
    {
        $orderDto = $this->orders->find($order);

        abort_if($orderDto === null, 404);

        $ownsAsUser = $request->user() && $orderDto->userId === $request->user()->id;
        $ownsAsGuest = session('last_guest_order_id') === $orderDto->id;

        abort_unless($ownsAsUser || $ownsAsGuest, 403);

        return view('front.checkout.thank-you', [
            'order' => $orderDto,
        ]);
    }
}
