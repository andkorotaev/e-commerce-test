<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->except('show')
            ->parameters(['categories' => 'categoryId']);

        Route::resource('products', ProductController::class)
            ->except('show')
            ->parameters(['products' => 'productId']);

        Route::resource('brands', BrandController::class)
            ->except('show')
            ->parameters(['brands' => 'brandId']);

        Route::resource('product-attributes', ProductAttributeController::class)
            ->except('show')
            ->parameters(['product-attributes' => 'productAttributeId']);

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{reviewId}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('reviews/{reviewId}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{orderId}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{userId}', [UserController::class, 'show'])->name('users.show');
    });
});
