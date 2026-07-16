<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\UpdateProfileRequest;
use App\Services\OrderService;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected WishlistService $wishlist,
        protected OrderService $orders,
    ) {}

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

    public function orders(Request $request): View
    {
        return view('front.account.orders', [
            'orders' => $this->orders->forUser($request->user()->id),
        ]);
    }

    public function wishlist(Request $request): View
    {
        return view('front.account.wishlist', [
            'products' => $this->wishlist->forUser($request->user()->id),
        ]);
    }
}
