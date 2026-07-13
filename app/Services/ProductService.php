<?php

namespace App\Services;

use App\Dto\Product\ProductDto;
use App\Dto\Product\ProductInputDto;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTranslationRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProductService
{
    public function __construct(
        protected ProductRepository $products,
        protected ProductTranslationRepository $translations,
        protected ProductImageRepository $images,
    ) {}

    /**
     * @return Collection<int, ProductDto>
     */
    public function list(): Collection
    {
        return $this->products->all();
    }

    public function find(int $id): ?ProductDto
    {
        return $this->products->find($id);
    }

    /**
     * @return Collection<int, ProductDto>
     */
    public function activeForCategory(int $categoryId): Collection
    {
        return $this->products->activeForCategory($categoryId);
    }

    public function findBySlug(string $slug, string $locale): ?ProductDto
    {
        return $this->products->findBySlug($slug, $locale);
    }

    /**
     * @throws RuntimeException
     */
    public function create(ProductInputDto $dto): ProductDto
    {
        // Images are stored before the transaction starts (same reasoning as
        // CategoryService::create()): file storage isn't part of the DB
        // transaction, so uploading first means we know exactly what to
        // clean up from disk if the transaction below fails.
        $uploadedPaths = $dto->newImages->map(fn ($file) => $file->store('products', 'public'))->all();

        try {
            return DB::transaction(function () use ($dto, $uploadedPaths) {
                $product = $this->products->create([
                    'category_id' => $dto->categoryId,
                    'sku' => $dto->sku,
                    'price' => $dto->price,
                    'old_price' => $dto->oldPrice,
                    'stock' => $dto->stock,
                    'is_active' => $dto->isActive,
                    'sort_order' => $dto->sortOrder,
                ]);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($product->id, $translation);
                }

                foreach ($uploadedPaths as $sortOrder => $path) {
                    $this->images->create($product->id, $path, $sortOrder);
                }

                return $this->products->find($product->id);
            });
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            throw new RuntimeException('Failed to create product.', previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function update(int $productId, ProductInputDto $dto): ProductDto
    {
        $existing = $this->products->find($productId);

        if (! $existing) {
            throw new RuntimeException("Product #{$productId} not found.");
        }

        $uploadedPaths = $dto->newImages->map(fn ($file) => $file->store('products', 'public'))->all();

        $imagesToDelete = $existing->images->filter(
            fn ($image) => $dto->deleteImageIds->contains($image->id)
        );

        try {
            $product = DB::transaction(function () use ($productId, $dto, $uploadedPaths, $imagesToDelete) {
                $product = $this->products->update($productId, [
                    'category_id' => $dto->categoryId,
                    'sku' => $dto->sku,
                    'price' => $dto->price,
                    'old_price' => $dto->oldPrice,
                    'stock' => $dto->stock,
                    'is_active' => $dto->isActive,
                    'sort_order' => $dto->sortOrder,
                ]);

                foreach ($dto->translations as $translation) {
                    $this->translations->upsert($productId, $translation);
                }

                foreach ($imagesToDelete as $image) {
                    $this->images->delete($image->id);
                }

                $nextSortOrder = $this->images->nextSortOrder($productId);
                foreach ($uploadedPaths as $offset => $path) {
                    $this->images->create($productId, $path, $nextSortOrder + $offset);
                }

                return $this->products->find($product->id);
            });

            // Only delete the actual files once the DB transaction has
            // committed — same rule as categories' single-image update:
            // don't destroy data before you're sure the DB agrees.
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->path);
            }

            return $product;
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            throw new RuntimeException("Failed to update product #{$productId}.", previous: $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function delete(int $productId): void
    {
        $product = $this->products->find($productId);

        if (! $product) {
            return;
        }

        try {
            DB::transaction(function () use ($productId) {
                $this->images->deleteForProduct($productId);
                $this->translations->deleteForProduct($productId);
                $this->products->delete($productId);
            });

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete product #{$productId}.", previous: $e);
        }
    }
}
