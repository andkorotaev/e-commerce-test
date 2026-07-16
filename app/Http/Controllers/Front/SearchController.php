<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\ProductListingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(protected ProductListingService $listing) {}

    public function index(Request $request): View
    {
        $query = trim((string) $request->string('q'));

        return view('front.search.index', [
            'query' => $query,
            'products' => $query !== '' ? $this->listing->search($query, $request->user()?->id) : null,
        ]);
    }
}
