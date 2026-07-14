@php
    $locale = app()->getLocale();
    $translation = $category->translation($locale);

    $chain = $ancestors->push($category)->values();
    $lastIndex = $chain->count() - 1;

    $breadcrumbs = $chain
        ->map(fn ($node, $index) => [
            'label' => $node->translation($locale)?->name,
            'url' => $index === $lastIndex ? null : route('front.categories.show', $node->translation($locale)?->slug),
        ])
        ->prepend(['label' => 'Головна', 'url' => route('front.home')]);
@endphp

<x-front.layouts.layout :title="$translation?->name">
    @if ($category->image)
        <div class="h-40 w-full overflow-hidden md:h-56">
            <img
                src="{{ Storage::url($category->image) }}"
                alt="{{ $translation?->name }}"
                class="h-full w-full object-cover"
            >
        </div>
    @endif

    <div class="mx-auto max-w-6xl px-4 py-6 md:px-10">
        <x-front.breadcrumbs :items="$breadcrumbs" />

        <div class="mb-6">
            <h1 class="font-serif text-2xl text-ink md:text-3xl">{{ $translation?->h1 ?? $translation?->name }}</h1>
            @if ($translation?->description)
                <p class="mt-1 max-w-md text-sm text-ink/60">{{ $translation->description }}</p>
            @endif
        </div>

        @if ($category->children->isNotEmpty())
            <div class="mb-8 grid grid-cols-3 gap-x-4 gap-y-6 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ($category->children as $index => $child)
                    <x-front.categories.card :category="$child" :index="$index" compact />
                @endforeach
            </div>
        @endif

        <x-front.products.listing :category="$category" :listing="$listing" :filters="$filters" />
    </div>
</x-front.layouts.layout>
