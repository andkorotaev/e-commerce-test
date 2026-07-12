<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categories) {}

    public function index(): View
    {
        return view('admin.categories.index', [
            'tree' => $this->categories->tree(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'parentOptions' => $this->categories->options(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->getDto());

        return redirect()->route('admin.categories.index');
    }

    public function edit(int $categoryId): View
    {
        $dto = $this->categories->find($categoryId);

        abort_if($dto === null, 404);

        return view('admin.categories.edit', [
            'category' => $dto,
            'parentOptions' => $this->categories->options(excludeId: $categoryId),
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $categoryId): RedirectResponse
    {
        $this->categories->update($categoryId, $request->getDto());

        return redirect()->route('admin.categories.index');
    }

    public function destroy(int $categoryId): RedirectResponse
    {
        $this->categories->delete($categoryId);

        return redirect()->route('admin.categories.index');
    }
}
