<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAttribute\StoreProductAttributeRequest;
use App\Http\Requests\Admin\ProductAttribute\UpdateProductAttributeRequest;
use App\Services\ProductAttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductAttributeController extends Controller
{
    public function __construct(protected ProductAttributeService $attributes) {}

    public function index(): View
    {
        return view('admin.product-attributes.index', [
            'attributes' => $this->attributes->list(),
        ]);
    }

    public function create(): View
    {
        return view('admin.product-attributes.create');
    }

    public function store(StoreProductAttributeRequest $request): RedirectResponse
    {
        $this->attributes->create($request->getDto());

        return redirect()->route('admin.product-attributes.index');
    }

    public function edit(int $productAttributeId): View
    {
        $dto = $this->attributes->find($productAttributeId);

        abort_if($dto === null, 404);

        return view('admin.product-attributes.edit', [
            'attribute' => $dto,
        ]);
    }

    public function update(UpdateProductAttributeRequest $request, int $productAttributeId): RedirectResponse
    {
        $this->attributes->update($productAttributeId, $request->getDto());

        return redirect()->route('admin.product-attributes.index');
    }

    public function destroy(int $productAttributeId): RedirectResponse
    {
        $this->attributes->delete($productAttributeId);

        return redirect()->route('admin.product-attributes.index');
    }
}
