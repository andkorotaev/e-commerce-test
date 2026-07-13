<?php

namespace App\Http\Requests\Admin\Product;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateProductRequest extends ProductRequest
{
    protected ?Product $productModel = null;

    /**
     * The route only carries the product ID (no implicit model binding —
     * the controller works with plain IDs, not Eloquent models). Rules below
     * still need the actual row to validate uniqueness-excluding-self, so
     * this fetches it lazily, once, on first use.
     */
    protected function product(): Product
    {
        return $this->productModel ??= Product::with('translations')
            ->findOrFail($this->route('productId'));
    }

    protected function uniqueSlugRule(string $locale): Unique
    {
        $translationId = $this->product()->translations->firstWhere('locale', $locale)?->id;

        return Rule::unique('product_translations', 'slug')
            ->where('locale', $locale)
            ->ignore($translationId);
    }

    protected function uniqueSkuRule(): Unique
    {
        return Rule::unique('products', 'sku')->ignore($this->product()->id);
    }
}
