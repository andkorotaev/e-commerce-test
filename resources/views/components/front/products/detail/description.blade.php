@props(['product'])

@php $translation = $product->translation(app()->getLocale()); @endphp

<div>
    <h2 class="mb-4 font-serif text-2xl text-ink">Опис</h2>
    <p class="max-w-xl whitespace-pre-line text-sm leading-relaxed text-ink/70">
        {{ $translation?->description ?? 'Опис незабаром.' }}
    </p>
</div>
