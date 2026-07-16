@props(['products'])

<section data-component="front/home/new-arrivals" class="mx-auto max-w-6xl px-4 py-16 md:px-10 md:py-20">
    <div class="mb-10 flex items-end justify-between">
        <div>
            <p class="font-mono text-xs uppercase tracking-widest text-stone">Щойно надійшло</p>
            <h2 class="mt-2 font-serif text-3xl text-ink">Новинки</h2>
        </div>

        <div class="hidden gap-2 sm:flex">
            <button
                type="button"
                data-scroll-prev
                aria-label="Попередні товари"
                class="flex h-9 w-9 items-center justify-center border border-stone text-ink/60 transition-colors hover:border-ink hover:text-ink"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                </svg>
            </button>
            <button
                type="button"
                data-scroll-next
                aria-label="Наступні товари"
                class="flex h-9 w-9 items-center justify-center border border-stone text-ink/60 transition-colors hover:border-ink hover:text-ink"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6" />
                </svg>
            </button>
        </div>
    </div>

    <div data-scroll-track class="flex snap-x snap-mandatory gap-6 overflow-x-auto pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($products as $index => $product)
            <div class="w-[45vw] shrink-0 snap-start sm:w-[260px]">
                <x-front.products.card :product="$product" :index="$index" />
            </div>
        @endforeach
    </div>
</section>
