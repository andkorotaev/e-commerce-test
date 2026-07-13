<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categories) {}

    public function show(string $slug): View
    {
        $category = $this->categories->findBySlug($slug, app()->getLocale());

        abort_if($category === null, 404);

        return view('front.categories.show', [
            'category' => $category,
        ]);
    }
}
