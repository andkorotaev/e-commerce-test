<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Auth\RegisterRequest;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function create(): View
    {
        return view('front.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $firstName = $request->string('first_name');
        $lastName = $request->string('last_name');

        $user = User::create([
            'name' => trim("$firstName $lastName"),
            'first_name' => $firstName,
            'last_name' => $lastName->isEmpty() ? null : $lastName,
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'password' => Hash::make($request->string('password')),
        ]);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        $this->cart->mergeGuestCartIntoUserCart($user);

        return redirect()->route('front.account.profile');
    }
}
