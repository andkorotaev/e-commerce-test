@props(['product', 'index' => 0])

{{--
    THE single product card shape, used everywhere a product grid appears
    (category listing, new arrivals, popular products, similar products,
    wishlist). Text areas are clamped with a matching min-height so every
    card renders the same size regardless of name/description length —
    that's what previously made branded products stick out.
--}}
<div
    class="group motion-safe:opacity-0 motion-safe:[animation:fade-in-up_0.6s_ease-out_forwards]"
    style="animation-delay: {{ min($index, 12) * 60 }}ms"
>
    <div class="relative">
        <a href="{{ $product->slug ? route('front.products.show', $product->slug) : '#' }}" class="block">
            <div class="relative aspect-[3/4] overflow-hidden bg-stone/10">
                @if ($product->image)
                    <img
                        src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                    >
                @endif

                @if ($product->stock <= 0)
                    <span class="absolute left-2 top-2 bg-ink px-2 py-1 font-mono text-[10px] uppercase tracking-widest text-bone">
                        Немає в наявності
                    </span>
                @endif
            </div>
        </a>

        <div class="absolute right-2 top-2 z-10">
            @auth
                <form method="POST" action="{{ route('front.wishlist.toggle', $product->id) }}">
                    @csrf
                    <button
                        type="submit"
                        aria-label="{{ $product->isWishlisted ? 'Видалити зі списку бажань' : 'Додати до списку бажань' }}"
                        class="flex h-8 w-8 items-center justify-center bg-bone/80 backdrop-blur-sm transition-colors hover:bg-bone {{ $product->isWishlisted ? 'text-madder' : 'text-ink/60' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ $product->isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.5C.5 8 2 4.5 5.5 4c2-.3 3.7.7 4.5 2 .8-1.3 2.5-2.3 4.5-2 3.5.5 5 4 3 7.5C19 15.65 12 20 12 20z" />
                        </svg>
                    </button>
                </form>
            @else
                <a
                    href="{{ route('front.login') }}"
                    aria-label="Додати до списку бажань"
                    class="flex h-8 w-8 items-center justify-center bg-bone/80 text-ink/60 backdrop-blur-sm transition-colors hover:bg-bone"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.5C.5 8 2 4.5 5.5 4c2-.3 3.7.7 4.5 2 .8-1.3 2.5-2.3 4.5-2 3.5.5 5 4 3 7.5C19 15.65 12 20 12 20z" />
                    </svg>
                </a>
            @endauth
        </div>
    </div>

    <a href="{{ $product->slug ? route('front.products.show', $product->slug) : '#' }}" class="block">
        <h3 class="mt-4 line-clamp-2 min-h-[2.5rem] text-base font-medium leading-tight text-ink transition-colors duration-300 group-hover:text-madder">
            {{ $product->name }}
        </h3>

        <p class="mt-1 line-clamp-2 min-h-[2rem] text-xs leading-4 text-ink/50">
            {{ $product->description }}
        </p>
    </a>

    <div class="mt-1 flex items-center gap-1.5" aria-hidden="true">
        <div class="flex items-center gap-0.5 text-madder">
            @for ($i = 1; $i <= 5; $i++)
                <svg viewBox="0 0 20 20" fill="{{ $i <= round($product->rating ?? 0) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1" class="h-3 w-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 1.5l2.6 5.6 6 .8-4.4 4.3 1 6-5.2-2.9-5.2 2.9 1-6L1.4 7.9l6-.8L10 1.5z" />
                </svg>
            @endfor
        </div>
        @if ($product->reviewsCount)
            <span class="font-mono text-[10px] text-ink/40">({{ $product->reviewsCount }})</span>
        @endif
    </div>

    <div class="mt-2 flex items-center justify-between gap-2">
        <p class="font-mono text-sm text-ink">
            @if ($product->oldPrice)
                <span class="mr-1.5 text-ink/30 line-through">{{ number_format($product->oldPrice, 0, ',', ' ') }} ₴</span>
            @endif
            {{ number_format($product->price, 0, ',', ' ') }} ₴
        </p>

        <form method="POST" action="{{ route('front.cart.add') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button
                type="submit"
                aria-label="Додати в кошик"
                {{ $product->stock <= 0 ? 'disabled' : '' }}
                class="flex h-8 w-8 shrink-0 items-center justify-center border border-stone text-ink/60 transition-all duration-300 group-hover:scale-110 group-hover:border-madder group-hover:bg-madder group-hover:text-bone disabled:cursor-not-allowed disabled:opacity-40 disabled:group-hover:scale-100 disabled:group-hover:border-stone disabled:group-hover:bg-transparent disabled:group-hover:text-ink/60"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-8H5.4M7 13L5.4 5M7 13l-1.6 4h12.2M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                </svg>
            </button>
        </form>
    </div>
</div>
