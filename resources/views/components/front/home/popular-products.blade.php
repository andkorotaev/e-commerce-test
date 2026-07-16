@props(['products'])

<section class="bg-stone/10 py-16 md:py-20">
    <div class="mx-auto max-w-6xl px-4 md:px-10">
        <div class="mb-10">
            <p class="font-mono text-xs uppercase tracking-widest text-stone">За відгуками покупців</p>
            <h2 class="mt-2 font-serif text-3xl text-ink">Популярні товари</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-12 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $index => $product)
                <x-front.products.card :product="$product" :index="$index" />
            @endforeach
        </div>
    </div>
</section>
