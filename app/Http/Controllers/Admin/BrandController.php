<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Admin\Brand\UpdateBrandRequest;
use App\Services\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(protected BrandService $brands) {}

    public function index(): View
    {
        return view('admin.brands.index', [
            'brands' => $this->brands->list(),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brands->create($request->getDto());

        return redirect()->route('admin.brands.index');
    }

    public function edit(int $brandId): View
    {
        $dto = $this->brands->find($brandId);

        abort_if($dto === null, 404);

        return view('admin.brands.edit', [
            'brand' => $dto,
        ]);
    }

    public function update(UpdateBrandRequest $request, int $brandId): RedirectResponse
    {
        $this->brands->update($brandId, $request->getDto());

        return redirect()->route('admin.brands.index');
    }

    public function destroy(int $brandId): RedirectResponse
    {
        $this->brands->delete($brandId);

        return redirect()->route('admin.brands.index');
    }
}
