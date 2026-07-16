<?php

use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\Auth\ForgotPasswordController;
use App\Http\Controllers\Front\Auth\LoginController;
use App\Http\Controllers\Front\Auth\RegisterController;
use App\Http\Controllers\Front\Auth\ResetPasswordController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/search', [SearchController::class, 'index'])->name('front.search');

// Purely static informational pages — no data to fetch, so a view route
// is enough (mirrors HomeController's own note that a controller is only
// worth promoting to once a route genuinely needs to fetch something).
Route::view('/about', 'front.about.index')->name('front.about');
Route::view('/contacts', 'front.contact.index')->name('front.contact');
Route::post('/contacts', [ContactController::class, 'store'])->name('front.contact.store');
Route::get('/catalog/{slug}/products', [CategoryController::class, 'products'])->name('front.categories.products');
Route::get('/catalog/{slug}', [CategoryController::class, 'show'])->name('front.categories.show');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('front.products.show');

// Not behind `auth` — a guest must be able to build up a cart (stored in a
// cookie) before ever creating an account.
Route::get('/cart', [CartController::class, 'show'])->name('front.cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('front.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('front.cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('front.cart.remove');

// Not behind `auth` — a guest can check out too (with an optional
// "create an account" checkbox handled inside the controller/service).
Route::get('/checkout', [CheckoutController::class, 'show'])->name('front.checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('front.checkout.store');
Route::get('/order/{order}/thank-you', [CheckoutController::class, 'thankYou'])->name('front.checkout.thank-you');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('front.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('front.register.store');
    Route::get('/login', [LoginController::class, 'create'])->name('front.login');
    Route::post('/login', [LoginController::class, 'store'])->name('front.login.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('front.password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('front.password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('front.password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('front.password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('front.logout');
    Route::post('/product/{slug}/reviews', [ReviewController::class, 'store'])->name('front.reviews.store');
    Route::post('/wishlist/{productId}/toggle', [WishlistController::class, 'toggle'])->name('front.wishlist.toggle');

    Route::prefix('account')->name('front.account.')->group(function () {
        Route::get('/', [AccountController::class, 'profile'])->name('profile');
        Route::put('/', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    });
});
