<?php

namespace App\Repositories;

use App\Dto\Category\CategoryDto;
use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepository
{
    /**
     * Flat list of every category (no tree structure — that's the service's job).
     *
     * @return Collection<int, CategoryDto>
     */
    public function all(): Collection
    {
        return Category::with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category) => CategoryDto::fromModel($category));
    }

    public function find(int $id): ?CategoryDto
    {
        $category = Category::with('translations')->find($id);

        return $category ? CategoryDto::fromModel($category) : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): CategoryDto
    {
        $category = Category::create($attributes);

        return CategoryDto::fromModel($category->load('translations'));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): CategoryDto
    {
        $category = Category::findOrFail($id);
        $category->update($attributes);

        return CategoryDto::fromModel($category->fresh('translations'));
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
    }
}
