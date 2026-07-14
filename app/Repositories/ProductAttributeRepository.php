<?php

namespace App\Repositories;

use App\Dto\ProductAttribute\ProductAttributeDto;
use App\Models\ProductAttribute;
use Illuminate\Support\Collection;

class ProductAttributeRepository
{
    protected const array WITH = ['translations', 'values.translations'];

    /**
     * @return Collection<int, ProductAttributeDto>
     */
    public function all(): Collection
    {
        return ProductAttribute::with(self::WITH)
            ->orderBy('id')
            ->get()
            ->map(fn (ProductAttribute $attribute) => ProductAttributeDto::fromModel($attribute));
    }

    public function find(int $id): ?ProductAttributeDto
    {
        $attribute = ProductAttribute::with(self::WITH)->find($id);

        return $attribute ? ProductAttributeDto::fromModel($attribute) : null;
    }

    /**
     * Looks an attribute up by its slug (e.g. "color"/"size") — the product
     * page's variation picker needs to know which of a variant's attribute
     * values is the color and which is the size.
     */
    public function findBySlug(string $slug): ?ProductAttributeDto
    {
        $attribute = ProductAttribute::where('slug', $slug)->with(self::WITH)->first();

        return $attribute ? ProductAttributeDto::fromModel($attribute) : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): ProductAttributeDto
    {
        $attribute = ProductAttribute::create($attributes);

        return ProductAttributeDto::fromModel($attribute->load(self::WITH));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): ProductAttributeDto
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->update($attributes);

        return ProductAttributeDto::fromModel($attribute->fresh(self::WITH));
    }

    public function delete(int $id): void
    {
        ProductAttribute::findOrFail($id)->delete();
    }
}
