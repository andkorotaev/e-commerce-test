@props(['product', 'brand', 'ratingStats'])

@php
    $translation = $product->translation(app()->getLocale());
    $rating = $ratingStats['average'];
    $count = $ratingStats['count'];

    $mod10 = $count % 10;
    $mod100 = $count % 100;
    $reviewWord = match (true) {
        $mod10 === 1 && $mod100 !== 11 => 'відгук',
        in_array($mod10, [2, 3, 4]) && ! in_array($mod100, [12, 13, 14]) => 'відгуки',
        default => 'відгуків',
    };
@endphp

<div>
    @if ($brand)
        <p class="font-mono text-xs uppercase tracking-widest text-ink/40">{{ $brand->name }}</p>
    @endif

    <h1 class="mt-1 font-serif text-3xl text-ink">{{ $translation?->h1 ?? $translation?->name }}</h1>

    <div class="mt-3 flex items-center gap-3">
        <div class="flex items-center gap-0.5 text-madder" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <svg viewBox="0 0 20 20" fill="{{ $i <= round($rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 1.5l2.6 5.6 6 .8-4.4 4.3 1 6-5.2-2.9-5.2 2.9 1-6L1.4 7.9l6-.8L10 1.5z" />
                </svg>
            @endfor
        </div>
        @if ($count > 0)
            <span class="font-mono text-xs text-ink/50">{{ number_format($rating, 1) }} · {{ $count }} {{ $reviewWord }}</span>
        @else
            <span class="font-mono text-xs text-ink/40">Ще немає відгуків</span>
        @endif
    </div>

    <p class="mt-4 font-mono text-2xl text-ink" data-product-price-display>
        @if ($product->oldPrice)
            <span class="mr-2 text-base text-ink/30 line-through">{{ number_format($product->oldPrice, 0, ',', ' ') }} ₴</span>
        @endif
        {{ number_format($product->price, 0, ',', ' ') }} ₴
    </p>

    <p class="mt-2 font-mono text-xs uppercase tracking-widest text-ink/40">Артикул: {{ $product->sku ?? '—' }}</p>

    <p class="mt-1 font-mono text-xs uppercase tracking-widest {{ $product->stock > 0 ? 'text-ink/50' : 'text-madder' }}" data-stock-status>
        {{ $product->stock > 0 ? 'В наявності' : 'Немає в наявності' }}
    </p>
</div>
