<?php

use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\Auth\ForgotPasswordController;
use App\Http\Controllers\Front\Auth\LoginController;
use App\Http\Controllers\Front\Auth\RegisterController;
use App\Http\Controllers\Front\Auth\ResetPasswordController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/catalog/{slug}/products', [CategoryController::class, 'products'])->name('front.categories.products');
Route::get('/catalog/{slug}', [CategoryController::class, 'show'])->name('front.categories.show');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('front.products.show');

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
