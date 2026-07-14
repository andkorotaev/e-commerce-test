@props(['product', 'brand', 'category', 'ancestors', 'colorAttributeId', 'sizeAttributeId', 'similar', 'ratingStats', 'reviews'])

@php
    $locale = app()->getLocale();
    $translation = $product->translation($locale);

    $breadcrumbs = collect();

    if ($category) {
        $chain = $ancestors->push($category)->values();

        $breadcrumbs = $chain->map(fn ($node) => [
            'label' => $node->translation($locale)?->name,
            'url' => route('front.categories.show', $node->translation($locale)?->slug),
        ]);
    }

    $breadcrumbs = $breadcrumbs
        ->prepend(['label' => 'Головна', 'url' => route('front.home')])
        ->push(['label' => $translation?->name, 'url' => null]);

    $colorValues = $product->variants
        ->flatMap->attributeValues
        ->filter(fn ($value) => $value->productAttributeId === $colorAttributeId)
        ->unique('id')
        ->values();

    $sizeValues = $product->variants
        ->flatMap->attributeValues
        ->filter(fn ($value) => $value->productAttributeId === $sizeAttributeId)
        ->unique('id')
        ->values();

    $variantsPayload = $product->variants->map(fn ($variant) => [
        'id' => $variant->id,
        'colorId' => $variant->attributeValues->firstWhere('productAttributeId', $colorAttributeId)?->id,
        'sizeId' => $variant->attributeValues->firstWhere('productAttributeId', $sizeAttributeId)?->id,
        'price' => $variant->price,
        'stock' => $variant->stock,
        'image' => $variant->image ? \Illuminate\Support\Facades\Storage::url($variant->image) : null,
    ])->values();
@endphp

<div data-component="front/products/detail" class="mx-auto max-w-6xl px-4 py-6 md:px-10">
    <x-front.breadcrumbs :items="$breadcrumbs" />

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
        <x-front.products.detail.gallery :product="$product" />

        <div>
            <x-front.products.detail.info :product="$product" :brand="$brand" :rating-stats="$ratingStats" />

            <x-front.products.detail.variations
                :product="$product"
                :color-values="$colorValues"
                :size-values="$sizeValues"
                :variants-payload="$variantsPayload"
                :has-size-guide="$sizeValues->isNotEmpty()"
            />
        </div>
    </div>

    <div class="mt-16 grid grid-cols-1 gap-16 lg:grid-cols-2">
        <x-front.products.detail.description :product="$product" />
        <x-front.products.detail.specifications :product="$product" :brand="$brand" :category="$category" :color-values="$colorValues" :size-values="$sizeValues" />
    </div>

    @if ($sizeValues->isNotEmpty())
        <x-front.products.detail.size-guide-modal :size-values="$sizeValues" />
    @endif

    <x-front.products.detail.reviews :product="$product" :rating-stats="$ratingStats" :reviews="$reviews" />

    @if ($similar->isNotEmpty())
        <x-front.products.detail.similar :products="$similar" />
    @endif
</div>
