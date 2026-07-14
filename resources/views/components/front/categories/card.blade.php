@props(['category', 'index' => 0, 'compact' => false])

@php $translation = $category->translation(app()->getLocale()); @endphp

<a
    href="{{ route('front.categories.show', $translation->slug) }}"
    class="group block motion-safe:opacity-0 motion-safe:[animation:fade-in-up_0.6s_ease-out_forwards]"
    style="animation-delay: {{ $index * 80 }}ms"
>
    <div class="{{ $compact ? 'aspect-square' : 'aspect-[3/4]' }} overflow-hidden bg-stone/10">
        @if ($category->image)
            <img
                src="{{ Storage::url($category->image) }}"
                alt="{{ $translation?->name }}"
                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
            >
        @endif
    </div>
    <h3 class="{{ $compact ? 'mt-2 text-sm' : 'mt-4 text-base' }} font-medium text-ink transition-colors duration-300 group-hover:text-madder">
        {{ $translation?->name }}
    </h3>
    @if ($translation?->description && ! $compact)
        <p class="mt-1 text-sm text-ink/50">{{ $translation->description }}</p>
    @endif
</a>
