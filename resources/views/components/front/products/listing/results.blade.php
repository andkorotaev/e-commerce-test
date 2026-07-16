@props(['listing'])

<div data-products-grid data-count="{{ $listing->products->total() }}">
    @if ($listing->products->isEmpty())
        <p class="font-mono text-xs uppercase tracking-widest text-ink/40">Товари незабаром</p>
    @else
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 xl:grid-cols-4">
            @foreach ($listing->products as $index => $product)
                <x-front.products.card :product="$product" :index="$index" />
            @endforeach
        </div>

        <div data-products-pagination class="mt-12">
            {{ $listing->products->links() }}
        </div>
    @endif
</div>
