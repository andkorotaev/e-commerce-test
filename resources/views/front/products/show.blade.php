@php $translation = $product->translation(app()->getLocale()); @endphp

<x-front.layouts.layout :title="$translation?->name">
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
