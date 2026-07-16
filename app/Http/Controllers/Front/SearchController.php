<?php

namespace App\Http\Controllers\Front;

use App\Dto\Product\ProductListItemDto;
use App\Http\Controllers\Controller;
use App\Services\ProductListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    /**
     * The search inputs' live autocomplete dropdown (header modal + catalog
     * toolbar) — a small JSON list, not a full page, fetched as the user
     * types.
     */
    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if ($query === '') {
            return response()->json(['results' => []]);
        }

        $results = $this->listing->suggestions($query)->map(fn (ProductListItemDto $product) => [
            'name' => $product->name,
            'image' => $product->image ? Storage::url($product->image) : null,
            'price' => number_format($product->price, 0, ',', ' ').' ₴',
            'url' => $product->slug ? route('front.products.show', $product->slug) : null,
        ])->values();

        return response()->json(['results' => $results]);
    }
}
