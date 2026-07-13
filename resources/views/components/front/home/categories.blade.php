@props(['categories'])

<section class="mx-auto max-w-6xl px-10 py-20">
    <div class="mb-10">
        <p class="font-mono text-xs uppercase tracking-widest text-stone">Каталог</p>
        <h2 class="mt-2 font-serif text-3xl text-ink">Категорії</h2>
    </div>

    <div class="grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-4">
        @foreach ($categories as $index => $category)
            <x-front.categories.card :category="$category" :index="$index" />
        @endforeach
    </div>
</section>
