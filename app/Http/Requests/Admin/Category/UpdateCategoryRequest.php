<?php

namespace App\Http\Requests\Admin\Category;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateCategoryRequest extends CategoryRequest
{
    protected ?Category $categoryModel = null;

    /**
     * The route only carries the category ID (no implicit model binding —
     * the controller works with plain IDs, not Eloquent models). Rules below
     * still need the actual row (translations, children) to validate
     * uniqueness-excluding-self and the circular-parent check, so this
     * fetches it lazily, once, on first use.
     */
    protected function category(): Category
    {
        return $this->categoryModel ??= Category::with(['translations', 'children'])
            ->findOrFail($this->route('categoryId'));
    }

    protected function uniqueSlugRule(string $locale): Unique
    {
        $translationId = $this->category()->translations->firstWhere('locale', $locale)?->id;

        return Rule::unique('category_translations', 'slug')
            ->where('locale', $locale)
            ->ignore($translationId);
    }

    protected function parentIsNotSelfOrDescendant(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! $value) {
                return;
            }

            $category = $this->category();

            if ((int) $value === $category->id) {
                $fail(__('A category cannot be its own parent.'));

                return;
            }

            if ($this->isDescendant($category, (int) $value)) {
                $fail(__('A category cannot be moved under one of its own subcategories.'));
            }
        };
    }

    protected function isDescendant(Category $category, int $candidateId): bool
    {
        foreach ($category->children as $child) {
            if ($child->id === $candidateId || $this->isDescendant($child, $candidateId)) {
                return true;
            }
        }

        return false;
    }
}
