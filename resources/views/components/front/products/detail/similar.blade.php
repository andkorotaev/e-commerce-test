@props(['products'])

<div class="mt-16 border-t border-stone/30 pt-12">
    <h2 class="mb-8 font-serif text-2xl text-ink">Схожі товари</h2>
    <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($products as $index => $item)
            <x-front.products.card :product="$item" :index="$index" />
        @endforeach
    </div>
</div>
