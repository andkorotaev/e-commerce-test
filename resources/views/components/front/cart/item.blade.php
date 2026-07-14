@props(['line'])

<div class="flex gap-4 py-6" data-cart-item data-product-id="{{ $line->productId }}" data-variant-id="{{ $line->variantId }}">
    <a href="{{ $line->slug ? route('front.products.show', $line->slug) : '#' }}" class="h-24 w-20 shrink-0 overflow-hidden bg-stone/10">
        @if ($line->image)
            <img src="{{ Storage::url($line->image) }}" alt="{{ $line->name }}" class="h-full w-full object-cover">
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-1">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ $line->slug ? route('front.products.show', $line->slug) : '#' }}" class="text-sm font-medium text-ink hover:text-madder">
                    {{ $line->name }}
                </a>
                @if ($line->variantLabel)
                    <p class="font-mono text-xs text-ink/40">{{ $line->variantLabel }}</p>
                @endif
            </div>

            <button
                type="button"
                data-cart-remove
                class="shrink-0 font-mono text-xs text-ink/40 underline decoration-dotted underline-offset-4 transition-colors hover:text-madder"
            >
                Видалити
            </button>
        </div>

        <div class="mt-auto flex items-center justify-between">
            <div class="inline-flex items-center border border-stone">
                <button type="button" data-cart-qty-decrease class="h-8 w-8 text-ink/60 transition-colors hover:text-ink">−</button>
                <input
                    type="number"
                    data-cart-qty-input
                    value="{{ $line->quantity }}"
                    min="1"
                    max="{{ $line->availableStock }}"
                    class="h-8 w-12 border-x border-stone bg-transparent text-center text-sm text-ink [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                >
                <button type="button" data-cart-qty-increase class="h-8 w-8 text-ink/60 transition-colors hover:text-ink">+</button>
            </div>

            <p class="font-mono text-sm text-ink">
                @if ($line->unitOldPrice && $line->unitOldPrice > $line->unitPrice)
                    <span class="mr-2 text-ink/30 line-through">
                        {{ number_format($line->unitOldPrice * $line->quantity, 0, ',', ' ') }} ₴
                    </span>
                @endif
                {{ number_format($line->lineTotal(), 0, ',', ' ') }} ₴
            </p>
        </div>
    </div>
</div>
