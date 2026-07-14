@props(['product', 'colorValues', 'sizeValues', 'variantsPayload', 'hasSizeGuide'])

@php
    $locale = app()->getLocale();

    $swatches = [
        'indigo' => '#3b4a6b',
        'walnut' => '#6b4a34',
        'cochineal' => '#a63b2c',
        'weld' => '#b7a33b',
        'bone' => '#ede6d8',
        'ink' => '#211c16',
        'stone' => '#b7afa0',
        'black' => '#1a1a1a',
        'white' => '#ffffff',
        'gray' => '#9c9c94',
        'red' => '#c23b2c',
        'blue' => '#2c4a7a',
        'gold' => '#c9a227',
        'brown' => '#6b4a34',
    ];
@endphp

<div
    data-component="front/products/detail/variations"
    class="mt-8 space-y-6"
    data-product-price="{{ $product->price }}"
    data-product-old-price="{{ $product->oldPrice }}"
    data-product-stock="{{ $product->stock }}"
>
    <script type="application/json" data-variants-payload>{!! json_encode($variantsPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    @if ($colorValues->isNotEmpty())
        <div>
            <h3 class="mb-2 font-mono text-xs uppercase tracking-widest text-ink/40">Колір</h3>
            <div class="flex flex-wrap gap-2" data-variation-group="color">
                @foreach ($colorValues as $value)
                    <button
                        type="button"
                        data-variation-option
                        data-value-id="{{ $value->id }}"
                        @if ($loop->first) data-selected="true" @endif
                        class="flex items-center gap-2 border border-stone px-3 py-1.5 text-sm text-ink/70 transition-colors data-[selected=true]:border-ink data-[selected=true]:text-ink"
                    >
                        <span class="inline-block h-3 w-3 rounded-full border border-ink/10" style="background-color: {{ $swatches[$value->slug] ?? '#b7afa0' }}"></span>
                        {{ $value->translation($locale)?->value ?? $value->slug }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if ($sizeValues->isNotEmpty())
        <div>
            <div class="mb-2 flex items-center justify-between">
                <h3 class="font-mono text-xs uppercase tracking-widest text-ink/40">Розмір</h3>
                @if ($hasSizeGuide)
                    <button type="button" data-size-guide-open class="font-mono text-xs text-ink/40 underline decoration-dotted underline-offset-4 hover:text-madder">
                        Таблиця розмірів
                    </button>
                @endif
            </div>
            <div class="flex flex-wrap gap-2" data-variation-group="size">
                @foreach ($sizeValues as $value)
                    <button
                        type="button"
                        data-variation-option
                        data-value-id="{{ $value->id }}"
                        @if ($loop->first) data-selected="true" @endif
                        class="min-w-11 border border-stone px-3 py-1.5 text-sm text-ink/70 transition-colors data-[selected=true]:border-ink data-[selected=true]:bg-ink data-[selected=true]:text-bone"
                    >
                        {{ $value->translation($locale)?->value ?? $value->slug }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <h3 class="mb-2 font-mono text-xs uppercase tracking-widest text-ink/40">Кількість</h3>
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center border border-stone">
                <button type="button" data-quantity-decrease class="h-10 w-10 text-ink/60 transition-colors hover:text-ink">−</button>
                <input type="number" data-quantity-input value="1" min="1" class="h-10 w-14 border-x border-stone bg-transparent text-center text-ink [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                <button type="button" data-quantity-increase class="h-10 w-10 text-ink/60 transition-colors hover:text-ink">+</button>
            </div>
            <span data-stock-hint class="font-mono text-xs text-ink/40"></span>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button
            type="button"
            data-add-to-cart
            class="flex-1 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder disabled:cursor-not-allowed disabled:bg-stone"
        >
            <span data-add-to-cart-label>Додати в кошик</span>
        </button>
        <button
            type="button"
            data-add-to-wishlist
            class="flex items-center justify-center gap-2 border border-stone px-6 py-3 font-mono text-xs uppercase tracking-widest text-ink/70 transition-colors hover:border-ink hover:text-ink data-[active=true]:border-madder data-[active=true]:text-madder"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.5C.5 8 2 4.5 5.5 4c2-.3 3.7.7 4.5 2 .8-1.3 2.5-2.3 4.5-2 3.5.5 5 4 3 7.5C19 15.65 12 20 12 20z" />
            </svg>
            <span data-add-to-wishlist-label>В обране</span>
        </button>
    </div>
</div>
