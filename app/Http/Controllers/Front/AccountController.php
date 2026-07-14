<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\UpdateProfileRequest;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(protected WishlistService $wishlist) {}

    public function profile(Request $request): View
    {
        return view('front.account.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->name = $request->string('name');
        $user->email = $request->string('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->string('password'));
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Order history — UI only for now: the storefront has no cart/checkout
     * flow yet, so no order is ever actually created. This renders the
     * table shape (number/date/amount/status) with a permanent empty state
     * rather than backing it with a table nothing writes to.
     */
    public function orders(): View
    {
        return view('front.account.orders');
    }

    public function wishlist(Request $request): View
    {
        return view('front.account.wishlist', [
            'products' => $this->wishlist->forUser($request->user()->id),
        ]);
    }
}
