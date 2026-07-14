@props(['product', 'brand', 'category', 'colorValues', 'sizeValues'])

@php
    $locale = app()->getLocale();

    $rows = array_filter([
        'Бренд' => $brand?->name,
        'Категорія' => $category?->translation($locale)?->name,
        'Артикул' => $product->sku,
        'Наявність' => $product->stock > 0 ? 'В наявності' : 'Немає в наявності',
        'Доступні кольори' => $colorValues->isNotEmpty()
            ? $colorValues->map(fn ($value) => $value->translation($locale)?->value ?? $value->slug)->implode(', ')
            : null,
        'Доступні розміри' => $sizeValues->isNotEmpty()
            ? $sizeValues->map(fn ($value) => $value->translation($locale)?->value ?? $value->slug)->implode(', ')
            : null,
    ], fn ($value) => $value !== null);
@endphp

<div>
    <h2 class="mb-4 font-serif text-2xl text-ink">Характеристики</h2>
    <table class="w-full max-w-xl text-sm">
        <tbody>
            @foreach ($rows as $label => $value)
                <tr class="border-b border-stone/20">
                    <td class="w-1/3 py-2 pr-4 font-mono text-xs uppercase tracking-widest text-ink/40">{{ $label }}</td>
                    <td class="py-2 text-ink/70">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
