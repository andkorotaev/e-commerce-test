<?php

namespace App\Services;

use App\Dto\Category\CategoryDto;
use App\Dto\Category\CategoryInputDto;
use App\Repositories\CategoryRepository;
use App\Repositories\CategoryTranslationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $categories,
        protected CategoryTranslationRepository $translations,
    ) {}

    public function find(int $id): ?CategoryDto
    {
        return $this->categories->find($id);
    }

    /**
     * @return Collection<int, CategoryDto>
     */
    public function roots(): Collection
    {
        return $this->categories->roots();
    }

    /**
     * Full active-only tree (root → L2 → L3) for the header nav dropdown —
     * unlike `tree()`, this excludes inactive categories, which the admin
     * tree deliberately doesn't (admins need to see and manage them).
     *
     * @return Collection<int, CategoryDto>
     */
    public function navigation(): Collection
    {
        return $this->buildTree($this->categories->activeAll(), null);
    }

    public function findBySlug(string $slug, string $locale): ?CategoryDto
    {
        return $this->categories->findBySlug($slug, $locale);
    }

    /**
     * A category's parent chain, root-first, NOT including the category
     * itself — e.g. for "Кросівки" (Sneakers) this returns
     * [Footwear] and NOT [Footwear, Sneakers]. Used to build breadcrumbs,
     * where the current category is rendered separately as the trail's
     * final, non-linked item.
     *
     * @return Collection<int, CategoryDto>
     */
    public function ancestors(int $categoryId): Collection
    {
        $byId = $this->categories->all()->keyBy('id');
        $chain = collect();
        $current = $byId->get($categoryId);

        while ($current && $current->parentId !== null) {
            $parent = $byId->get($current->parentId);

            if (! $parent) {
                break;
            }

            $chain->prepend($parent);
            $current = $parent;
        }

        return $chain;
    }

    /**
     * A category's own id plus every descendant id (children, grandchildren,
     * ...) — the storefront product listing scopes to this so viewing a
     * parent category (e.g. "Footwear") shows products from every leaf
     * category underneath it, not just products assigned to that exact row.
     *
     * @return array<int, int>
     */
    public function descendantIds(int $categoryId): array
    {
        $all = $this->categories->all();
        $ids = [$categoryId];

        $collect = function (int $parentId) use ($all, &$ids, &$collect): void {
            foreach ($all as $category) {
                if ($category->parentId === $parentId) {
                    $ids[] = $category->id;
                    $collect($category->id);
                }
            }
        };

        $collect($categoryId);

        return $ids;
    }

    /**
     * @return Collection<int, CategoryDto>
     */
    public function tree(): Collection
    {
        return $this->buildTree($this->categories->all(), null);
    }

    /**
     * Flat, indent-labelled `[id => label]` list for a parent-select dropdown.
     * Pass $excludeId (e.g. the category being edited) to omit it and all of
     * its descendants, so the form can't offer an invalid/circular parent.
     *
     * @return array<int, string>
     */
    public function options(?int $excludeId = null): array
    {
        $options = [];

        $this->flattenForOptions($this->tree(), 0, $excludeId, $options);

        return $options;
    }

    /**
     * @throws RuntimeException
     */
    public function create(CategoryInputDto $dto): CategoryDto
    {
        $imagePath = null;

        try {
            $imagePath = $dto->image?->store('categories', 'public');

            return DB::transaction(function () use ($dto, $imagePath) {
                $category = $this->categories->create([
                    'parent_id' => $dto->parentId,
                    'is_active' => $dto->isActive,
                    'sort_order' => $dto->sortOrder,
                    'image' => $imagePath,
                ]);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($category->id, $translation);
                }

                return $this->categories->find($category->id);
            });
        } catch (Throwable $e) {
            // The DB transaction already rolled itself back; the uploaded
            // file is not part of that transaction, so it has to be cleaned
            // up by hand or it's left orphaned on disk.
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            report($e);

            throw new RuntimeException('Failed to create category.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function update(int $categoryId, CategoryInputDto $dto): CategoryDto
    {
        $existing = $this->categories->find($categoryId);

        if (! $existing) {
            throw new RuntimeException("Category #{$categoryId} not found.");
        }

        $newImagePath = null;

        try {
            $attributes = [
                'parent_id' => $dto->parentId,
                'is_active' => $dto->isActive,
                'sort_order' => $dto->sortOrder,
            ];

            if ($dto->image !== null) {
                $newImagePath = $dto->image->store('categories', 'public');
                $attributes['image'] = $newImagePath;
            }

            $category = DB::transaction(function () use ($categoryId, $attributes, $dto) {
                $category = $this->categories->update($categoryId, $attributes);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($categoryId, $translation);
                }

                return $this->categories->find($category->id);
            });

            // Only delete the old image once the new one is safely committed —
            // deleting it up front would leave a broken image reference if the
            // transaction below had failed.
            if ($newImagePath && $existing->image) {
                Storage::disk('public')->delete($existing->image);
            }

            return $category;
        } catch (Throwable $e) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            report($e);

            throw new RuntimeException("Failed to update category #{$categoryId}.", previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $categoryId): void
    {
        $category = $this->categories->find($categoryId);

        if (! $category) {
            return;
        }

        try {
            DB::transaction(function () use ($categoryId) {
                $this->translations->deleteForCategory($categoryId);
                $this->categories->delete($categoryId);
            });

            // Only delete the file once the DB rows are actually gone — if the
            // transaction had failed, the category still exists and still
            // needs its image.
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete category #{$categoryId}.", previous: $e);
        }
    }

    /**
     * @param  Collection<int, CategoryDto>  $categories
     * @return Collection<int, CategoryDto>
     */
    protected function buildTree(Collection $categories, ?int $parentId): Collection
    {
        return $categories->where('parentId', $parentId)->values()->map(
            fn (CategoryDto $category) => $category->withChildren(
                $this->buildTree($categories, $category->id)
            )
        );
    }

    /**
     * @param  Collection<int, CategoryDto>  $categories
     * @param  array<int, string>  $options
     */
    protected function flattenForOptions(Collection $categories, int $depth, ?int $excludeId, array &$options): void
    {
        foreach ($categories as $category) {
            if ($category->id === $excludeId) {
                continue;
            }

            $label = str_repeat('— ', $depth).($category->translation('uk')?->name ?? '—');
            $options[$category->id] = $label;

            $this->flattenForOptions($category->children, $depth + 1, $excludeId, $options);
        }
    }
}
