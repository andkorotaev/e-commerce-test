@props(['product'])

@php
    $locale = app()->getLocale();
    $images = $product->images;
    $name = $product->translation($locale)?->name;
@endphp

<div data-component="front/products/detail/gallery">
    <div data-gallery-zoom class="relative aspect-square cursor-zoom-in overflow-hidden bg-stone/10">
        <img
            data-gallery-main
            src="{{ $images->isNotEmpty() ? Storage::url($images->first()->path) : '' }}"
            alt="{{ $name }}"
            class="h-full w-full object-cover transition-transform duration-300 ease-out"
        >
    </div>

    @if ($images->count() > 1)
        <div class="mt-4 grid grid-cols-5 gap-3">
            @foreach ($images as $image)
                <button
                    type="button"
                    data-gallery-thumb
                    data-full="{{ Storage::url($image->path) }}"
                    @if ($loop->first) data-active="true" @endif
                    class="aspect-square overflow-hidden bg-stone/10 opacity-60 ring-1 ring-transparent transition-all data-[active=true]:opacity-100 data-[active=true]:ring-ink"
                >
                    <img src="{{ Storage::url($image->path) }}" alt="" class="h-full w-full object-cover">
                </button>
            @endforeach
        </div>
    @endif
</div>
