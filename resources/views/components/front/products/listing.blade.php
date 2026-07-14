@props(['category', 'listing', 'filters'])

@php $slug = $category->translation(app()->getLocale())?->slug; @endphp

<div data-component="front/products/listing" class="mt-16">
    <form
        data-products-form
        action="{{ route('front.categories.show', $slug) }}"
        data-products-url="{{ route('front.categories.products', $slug) }}"
        method="GET"
        class="grid grid-cols-1 gap-10 lg:grid-cols-[240px_1fr]"
    >
        <x-front.products.listing.filters :category="$category" :listing="$listing" :filters="$filters" />

        <div class="min-w-0">
            <x-front.products.listing.toolbar :listing="$listing" :filters="$filters" />

            <div data-products-grid data-count="{{ $listing->products->total() }}">
                <x-front.products.listing.results :listing="$listing" />
            </div>
        </div>
    </form>
</div>
