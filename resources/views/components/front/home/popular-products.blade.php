@props(['products'])

<section class="bg-stone/10 py-16 md:py-20">
    <div class="mx-auto max-w-6xl px-4 md:px-10">
        <div class="mb-10">
            <p class="font-mono text-xs uppercase tracking-widest text-stone">За відгуками покупців</p>
            <h2 class="mt-2 font-serif text-3xl text-ink">Популярні товари</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $index => $product)
                <div
                    class="motion-safe:opacity-0 motion-safe:[animation:fade-in-up_0.6s_ease-out_forwards]"
                    style="animation-delay: {{ min($index, 12) * 60 }}ms"
                >
                    <a href="{{ $product->slug ? route('front.products.show', $product->slug) : '#' }}" class="group block">
                        <div class="relative aspect-[3/4] overflow-hidden bg-white">
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
                                class="flex h-8 w-8 shrink-0 items-center justify-center border border-stone text-ink/60 transition-colors hover:border-ink hover:bg-ink hover:text-bone disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-8H5.4M7 13L5.4 5M7 13l-1.6 4h12.2M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
