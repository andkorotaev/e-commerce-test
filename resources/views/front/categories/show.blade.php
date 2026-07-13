@php $translation = $category->translation(app()->getLocale()); @endphp

<x-front.layouts.layout :title="$translation?->name">
    <div class="mx-auto max-w-6xl px-10 py-16">
        <a href="{{ route('front.home') }}" class="mb-8 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Головна
        </a>

        <div class="mb-14 flex flex-col gap-8 md:flex-row md:items-end">
            @if ($category->image)
                <div class="aspect-[3/2] w-full overflow-hidden md:w-80">
                    <img
                        src="{{ Storage::url($category->image) }}"
                        alt="{{ $translation?->name }}"
                        class="h-full w-full object-cover"
                    >
                </div>
            @endif
            <div>
                <h1 class="font-serif text-4xl text-ink">{{ $translation?->h1 ?? $translation?->name }}</h1>
                @if ($translation?->description)
                    <p class="mt-3 max-w-md text-ink/60">{{ $translation->description }}</p>
                @endif
            </div>
        </div>

        @if ($category->children->isNotEmpty())
            <div class="grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-4">
                @foreach ($category->children as $index => $child)
                    <x-front.categories.card :category="$child" :index="$index" />
                @endforeach
            </div>
        @else
            <p class="font-mono text-xs uppercase tracking-widest text-ink/40">Товари незабаром</p>
        @endif
    </div>
</x-front.layouts.layout>
