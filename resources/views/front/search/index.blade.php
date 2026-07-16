<x-front.layouts.layout :title="$query !== '' ? 'Пошук: '.$query : 'Пошук'">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-10">
        <h1 class="mb-2 font-serif text-3xl text-ink">Пошук</h1>

        @if ($query === '')
            <p class="font-mono text-xs uppercase tracking-widest text-ink/40">Введіть пошуковий запит</p>
        @else
            <p class="mb-10 font-mono text-xs uppercase tracking-widest text-ink/40">
                @if ($products->total() > 0)
                    За запитом «{{ $query }}» знайдено {{ $products->total() }}
                @else
                    За запитом «{{ $query }}» нічого не знайдено
                @endif
            </p>

            @if ($products->isNotEmpty())
                <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($products as $index => $product)
                        <x-front.products.card :product="$product" :index="$index" />
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
</x-front.layouts.layout>
