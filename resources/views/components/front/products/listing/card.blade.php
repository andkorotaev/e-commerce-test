@props(['product', 'index' => 0])

<a
    href="{{ $product->slug ? route('front.products.show', $product->slug) : '#' }}"
    class="group block motion-safe:opacity-0 motion-safe:[animation:fade-in-up_0.6s_ease-out_forwards]"
    style="animation-delay: {{ min($index, 12) * 60 }}ms"
>
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

    @if ($product->brandName)
        <p class="mt-4 font-mono text-[10px] uppercase tracking-widest text-ink/40">{{ $product->brandName }}</p>
    @endif

    <h3 class="mt-1 text-base font-medium text-ink transition-colors duration-300 group-hover:text-madder">
        {{ $product->name }}
    </h3>

    <p class="mt-1 font-mono text-sm text-ink/70">
        @if ($product->oldPrice)
            <span class="mr-2 text-ink/30 line-through">{{ number_format($product->oldPrice, 0, ',', ' ') }} ₴</span>
        @endif
        {{ number_format($product->price, 0, ',', ' ') }} ₴
    </p>
</a>
