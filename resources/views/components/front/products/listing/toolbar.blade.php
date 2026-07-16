@props(['listing', 'filters'])

<div class="mb-6 flex flex-col gap-4 border-b border-stone/30 pb-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="font-mono text-xs uppercase tracking-widest text-ink/40">
        Знайдено <span data-products-count>{{ $listing->products->total() }}</span> товарів
    </div>

    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <div class="w-full sm:max-w-xs">
            <input
                type="search"
                name="search"
                value="{{ $filters->search }}"
                data-debounce
                data-suggest-url="{{ route('front.search.suggest') }}"
                placeholder="Пошук товарів…"
                class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink placeholder:text-ink/30 focus:border-ink focus:outline-none"
            >
        </div>

        <select
            name="sort"
            class="border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
        >
            <option value="popularity" @selected($filters->sort === 'popularity')>За популярністю</option>
            <option value="newest" @selected($filters->sort === 'newest')>За новизною</option>
            <option value="price_asc" @selected($filters->sort === 'price_asc')>Ціна: за зростанням</option>
            <option value="price_desc" @selected($filters->sort === 'price_desc')>Ціна: за спаданням</option>
            <option value="name" @selected($filters->sort === 'name')>За назвою</option>
        </select>
    </div>
</div>
