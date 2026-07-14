<?php

namespace App\Services;

use App\Dto\Product\ProductDto;
use App\Dto\Product\ProductInputDto;
use App\Dto\ProductVariant\ProductVariantInputDto;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTranslationRepository;
use App\Repositories\ProductVariantRepository;
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
        protected ProductVariantRepository $variants,
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
     * @return Collection<int, \App\Dto\Product\ProductListItemDto>
     */
    public function similarTo(ProductDto $product, int $limit = 8): Collection
    {
        return $this->products->similarTo($product->categoryId, $product->id, $limit);
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

        // Same reasoning as the product images above: variant images are
        // uploaded up front so we know exactly what to remove from disk if
        // the transaction fails.
        $variantImagePaths = $dto->variants->map(
            fn (ProductVariantInputDto $variant) => $variant->image?->store('products/variants', 'public')
        );

        try {
            return DB::transaction(function () use ($dto, $uploadedPaths, $variantImagePaths) {
                $product = $this->products->create([
                    'category_id' => $dto->categoryId,
                    'brand_id' => $dto->brandId,
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

                foreach ($dto->variants as $index => $variant) {
                    $this->variants->create($product->id, [
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                        'image' => $variantImagePaths[$index],
                        'is_active' => $variant->isActive,
                    ], $variant->attributeValueIds->all());
                }

                return $this->products->find($product->id);
            });
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            foreach ($variantImagePaths->filter() as $path) {
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

        $existingVariantsById = $existing->variants->keyBy('id');

        // For each kept/new variant: upload a new image if one was given,
        // otherwise carry its existing path forward untouched.
        $variantImagePaths = $dto->variants->map(function (ProductVariantInputDto $variant) use ($existingVariantsById) {
            if ($variant->image !== null) {
                return $variant->image->store('products/variants', 'public');
            }

            return $variant->id !== null ? $existingVariantsById->get($variant->id)?->image : null;
        });

        // A variant row survives only if its id is resubmitted — unlike
        // images (which can't be resubmitted through a file input, hence the
        // explicit delete_images checkbox), every other variant field is a
        // normal input the form re-renders with its current value. So the
        // submitted set of ids IS the desired end state; anything missing is
        // gone, the same "submit the whole thing, deleteExcept prunes the
        // rest" rule as ProductAttributeService's nested values.
        $keptVariantIds = $dto->variants->pluck('id')->filter()->all();
        $variantsToDelete = $existing->variants->reject(
            fn ($variant) => in_array($variant->id, $keptVariantIds, true)
        );

        // Old files to remove once the transaction commits: deleted variants'
        // images, plus any existing variant whose image got replaced above.
        $variantImagePathsToDelete = $variantsToDelete->pluck('image')->filter()
            ->merge(
                $dto->variants
                    ->filter(fn (ProductVariantInputDto $variant) => $variant->id !== null && $variant->image !== null)
                    ->map(fn (ProductVariantInputDto $variant) => $existingVariantsById->get($variant->id)?->image)
                    ->filter()
            );

        try {
            $product = DB::transaction(function () use ($productId, $dto, $uploadedPaths, $imagesToDelete, $variantImagePaths, $keptVariantIds) {
                $product = $this->products->update($productId, [
                    'category_id' => $dto->categoryId,
                    'brand_id' => $dto->brandId,
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

                $this->variants->deleteExcept($productId, $keptVariantIds);

                foreach ($dto->variants as $index => $variant) {
                    $attributes = [
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                        'image' => $variantImagePaths[$index],
                        'is_active' => $variant->isActive,
                    ];

                    if ($variant->id !== null) {
                        $this->variants->update($variant->id, $attributes, $variant->attributeValueIds->all());
                    } else {
                        $this->variants->create($productId, $attributes, $variant->attributeValueIds->all());
                    }
                }

                return $this->products->find($product->id);
            });

            // Only delete the actual files once the DB transaction has
            // committed — same rule as categories' single-image update:
            // don't destroy data before you're sure the DB agrees.
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->path);
            }

            foreach ($variantImagePathsToDelete as $path) {
                Storage::disk('public')->delete($path);
            }

            return $product;
        } catch (Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            foreach ($dto->variants as $index => $variant) {
                if ($variant->image !== null) {
                    Storage::disk('public')->delete($variantImagePaths[$index]);
                }
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
                $this->variants->deleteForProduct($productId);
                $this->translations->deleteForProduct($productId);
                $this->products->delete($productId);
            });

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
            }

            foreach ($product->variants as $variant) {
                if ($variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
            }
        } catch (Throwable $e) {
            report($e);

            throw new RuntimeException("Failed to delete product #{$productId}.", previous: $e);
        }
    }
}
