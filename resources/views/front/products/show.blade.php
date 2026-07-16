@php
    $translation = $product->translation(app()->getLocale());
    $metaDescription = $translation?->metaDescription
        ?? ($translation?->description ? \Illuminate\Support\Str::limit($translation->description, 160) : null);
@endphp

<x-front.layouts.layout
    :title="$translation?->name"
    :description="$metaDescription"
    :image="$product->primaryImage()?->path"
>
    <x-front.products.detail
        :product="$product"
        :brand="$brand"
        :category="$category"
        :ancestors="$ancestors"
        :color-attribute-id="$colorAttributeId"
        :size-attribute-id="$sizeAttributeId"
        :similar="$similar"
        :rating-stats="$ratingStats"
        :reviews="$reviews"
        :is-wishlisted="$isWishlisted"
    />
</x-front.layouts.layout>
