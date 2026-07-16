@props(['products'])

@if ($products->isEmpty())
    <p class="font-mono text-xs uppercase tracking-widest text-ink/40">Список бажань порожній</p>
@else
    <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($products as $index => $product)
            <x-front.products.card :product="$product" :index="$index" />
        @endforeach
    </div>
@endif
