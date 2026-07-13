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
     * Active, top-level categories only — for storefront display (e.g. the
     * homepage category grid), not the admin tree which shows everything.
     *
     * @return Collection<int, CategoryDto>
     */
    public function roots(): Collection
    {
        return Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category) => CategoryDto::fromModel($category));
    }

    /**
     * A single active category by its translated slug, with its own active
     * children attached (so the front category page can list them without a
     * second round trip). Returns null if no active category has that slug
     * in the given locale.
     */
    public function findBySlug(string $slug, string $locale): ?CategoryDto
    {
        $category = Category::where('is_active', true)
            ->whereHas('translations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)->where('locale', $locale);
            })
            ->with('translations')
            ->first();

        if (! $category) {
            return null;
        }

        $children = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $child) => CategoryDto::fromModel($child));

        return CategoryDto::fromModel($category)->withChildren($children);
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
