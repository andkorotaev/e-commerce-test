<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected CategoryService $categories) {}

    public function index(): View
    {
        return view('front.home.index', [
            'categories' => $this->categories->roots(),
        ]);
    }
}
