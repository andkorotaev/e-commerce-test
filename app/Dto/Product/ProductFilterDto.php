<?php

namespace App\Dto\Product;

use Illuminate\Support\Collection;

/**
 * Input DTO for the storefront category product listing — built from
 * validated query-string params by ProductFilterRequest::getFilterDto().
 * Unlike the write-side ...InputDto convention, this backs a read (GET)
 * request, but follows the same "FormRequest builds and returns a DTO" idiom.
 */
final readonly class ProductFilterDto
{
    /**
     * @param  Collection<int, int>  $categoryIds  selected child-category checkboxes, narrows the default subtree scope
     * @param  Collection<int, int>  $brandIds
     * @param  Collection<int, int>  $colorIds
     * @param  Collection<int, int>  $sizeIds
     */
    public function __construct(
        public Collection $categoryIds,
        public Collection $brandIds,
        public Collection $colorIds,
        public Collection $sizeIds,
        public ?float $priceMin,
        public ?float $priceMax,
        public bool $inStockOnly,
        public ?string $search,
        public string $sort,
        public int $perPage = 12,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $toIntCollection = fn (mixed $values) => collect($values ?? [])->map(fn ($id) => (int) $id)->values();

        return new self(
            categoryIds: $toIntCollection($data['category'] ?? []),
            brandIds: $toIntCollection($data['brand'] ?? []),
            colorIds: $toIntCollection($data['color'] ?? []),
            sizeIds: $toIntCollection($data['size'] ?? []),
            priceMin: isset($data['price_min']) && $data['price_min'] !== '' ? (float) $data['price_min'] : null,
            priceMax: isset($data['price_max']) && $data['price_max'] !== '' ? (float) $data['price_max'] : null,
            inStockOnly: (bool) ($data['in_stock'] ?? false),
            search: isset($data['search']) && $data['search'] !== '' ? $data['search'] : null,
            sort: $data['sort'] ?? 'popularity',
        );
    }
}
