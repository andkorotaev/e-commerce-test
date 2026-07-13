<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $products,
        protected CategoryService $categories,
    ) {}

    public function index(): View
    {
        return view('admin.products.index', [
            'products' => $this->products->list(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categoryOptions' => $this->categories->options(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->products->create($request->getDto());

        return redirect()->route('admin.products.index');
    }

    public function edit(int $productId): View
    {
        $dto = $this->products->find($productId);

        abort_if($dto === null, 404);

        return view('admin.products.edit', [
            'product' => $dto,
            'categoryOptions' => $this->categories->options(),
        ]);
    }

    public function update(UpdateProductRequest $request, int $productId): RedirectResponse
    {
        $this->products->update($productId, $request->getDto());

        return redirect()->route('admin.products.index');
    }

    public function destroy(int $productId): RedirectResponse
    {
        $this->products->delete($productId);

        return redirect()->route('admin.products.index');
    }
}
