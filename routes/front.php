<?php

use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/catalog/{slug}/products', [CategoryController::class, 'products'])->name('front.categories.products');
Route::get('/catalog/{slug}', [CategoryController::class, 'show'])->name('front.categories.show');
