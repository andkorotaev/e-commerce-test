@props(['category', 'listing', 'filters'])

@php
    $locale = app()->getLocale();

    $swatches = [
        'indigo' => '#3b4a6b',
        'walnut' => '#6b4a34',
        'cochineal' => '#a63b2c',
        'weld' => '#b7a33b',
        'bone' => '#ede6d8',
        'ink' => '#211c16',
        'stone' => '#b7afa0',
        'black' => '#1a1a1a',
        'white' => '#ffffff',
        'gray' => '#9c9c94',
        'red' => '#c23b2c',
        'blue' => '#2c4a7a',
        'gold' => '#c9a227',
        'brown' => '#6b4a34',
    ];
@endphp

<aside class="space-y-8 font-mono text-xs">
    @if ($category->children->isNotEmpty())
        <div>
            <h3 class="mb-3 uppercase tracking-widest text-ink/40">Категорія</h3>
            <div class="space-y-2">
                @foreach ($category->children as $child)
                    <label class="flex items-center gap-2 text-ink/70 hover:text-ink">
                        <input
                            type="checkbox"
                            name="category[]"
                            value="{{ $child->id }}"
                            @checked($filters->categoryIds->contains($child->id))
                            class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder"
                        >
                        {{ $child->translation($locale)?->name }}
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    @if ($listing->brands->isNotEmpty())
        <div>
            <h3 class="mb-3 uppercase tracking-widest text-ink/40">Бренд</h3>
            <div class="space-y-2">
                @foreach ($listing->brands as $brand)
                    <label class="flex items-center gap-2 text-ink/70 hover:text-ink">
                        <input
                            type="checkbox"
                            name="brand[]"
                            value="{{ $brand->id }}"
                            @checked($filters->brandIds->contains($brand->id))
                            class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder"
                        >
                        {{ $brand->name }}
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    @if ($listing->colors->isNotEmpty())
        <div>
            <h3 class="mb-3 uppercase tracking-widest text-ink/40">Колір</h3>
            <div class="space-y-2">
                @foreach ($listing->colors as $color)
                    <label class="flex items-center gap-2 text-ink/70 hover:text-ink">
                        <input
                            type="checkbox"
                            name="color[]"
                            value="{{ $color->id }}"
                            @checked($filters->colorIds->contains($color->id))
                            class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder"
                        >
                        <span
                            class="inline-block h-3 w-3 rounded-full border border-ink/10"
                            style="background-color: {{ $swatches[$color->slug] ?? '#b7afa0' }}"
                        ></span>
                        {{ $color->translation($locale)?->value ?? $color->slug }}
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    @if ($listing->sizes->isNotEmpty())
        <div>
            <h3 class="mb-3 uppercase tracking-widest text-ink/40">Розмір</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($listing->sizes as $size)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="size[]" value="{{ $size->id }}" @checked($filters->sizeIds->contains($size->id)) class="peer sr-only">
                        <span class="block rounded-none border border-stone px-2.5 py-1 text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                            {{ $size->translation($locale)?->value ?? $size->slug }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <h3 class="mb-3 uppercase tracking-widest text-ink/40">Ціна</h3>
        <div class="flex items-center gap-2">
            <input
                type="number"
                name="price_min"
                value="{{ $filters->priceMin }}"
                placeholder="{{ (int) $listing->priceMin }}"
                data-debounce
                min="0"
                class="w-full border border-stone bg-transparent px-2 py-1.5 text-ink placeholder:text-ink/30 focus:border-ink focus:outline-none"
            >
            <span class="text-ink/30">—</span>
            <input
                type="number"
                name="price_max"
                value="{{ $filters->priceMax }}"
                placeholder="{{ (int) $listing->priceMax }}"
                data-debounce
                min="0"
                class="w-full border border-stone bg-transparent px-2 py-1.5 text-ink placeholder:text-ink/30 focus:border-ink focus:outline-none"
            >
        </div>
    </div>

    <div>
        <label class="flex items-center gap-2 text-ink/70 hover:text-ink">
            <input
                type="checkbox"
                name="in_stock"
                value="1"
                @checked($filters->inStockOnly)
                class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder"
            >
            Лише в наявності
        </label>
    </div>

    <button
        type="button"
        data-products-reset
        class="uppercase tracking-widest text-ink/40 underline decoration-dotted underline-offset-4 hover:text-madder"
    >
        Скинути фільтри
    </button>
</aside>
