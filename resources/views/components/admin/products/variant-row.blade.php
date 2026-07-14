@props(['rowKey', 'variant' => null, 'productAttributes' => []])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1 block text-xs font-medium text-ink/60';
@endphp

<div data-variant-row class="rounded-md border border-ink/10 p-4">
    @if ($variant)
        <input type="hidden" name="variants[{{ $rowKey }}][id]" value="{{ $variant->id }}">
    @endif

    <div class="grid grid-cols-[repeat(auto-fit,minmax(140px,1fr))] gap-3">
        @foreach ($productAttributes as $attribute)
            @php
                $selectedValueId = $variant?->attributeValues->first(
                    fn ($value) => $value->productAttributeId === $attribute->id
                )?->id;
            @endphp
            <div>
                <label class="{{ $labelClass }}">{{ $attribute->translation('uk')?->name ?? $attribute->slug }}</label>
                <select name="variants[{{ $rowKey }}][attribute_value_ids][]" class="{{ $inputClass }}">
                    <option value="">— Select —</option>
                    @foreach ($attribute->values as $value)
                        <option value="{{ $value->id }}" @selected($selectedValueId === $value->id)>
                            {{ $value->translation('uk')?->value ?? $value->slug }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endforeach

        <div>
            <label class="{{ $labelClass }}">SKU</label>
            <input type="text" name="variants[{{ $rowKey }}][sku]" value="{{ $variant?->sku }}" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">Price <span class="font-normal text-ink/30">(optional)</span></label>
            <input type="number" step="0.01" name="variants[{{ $rowKey }}][price]" value="{{ $variant?->price }}" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">Stock</label>
            <input type="number" name="variants[{{ $rowKey }}][stock]" value="{{ $variant?->stock ?? 0 }}" class="{{ $inputClass }}">
        </div>
    </div>

    <div class="mt-3 flex items-end gap-4">
        <div class="flex-1">
            <label class="{{ $labelClass }}">Image <span class="font-normal text-ink/30">(optional)</span></label>
            <div class="flex items-center gap-3">
                @if ($variant?->image)
                    <img src="{{ Storage::url($variant->image) }}" class="h-10 w-10 rounded-md object-cover ring-1 ring-ink/10" alt="">
                @endif
                <input
                    type="file"
                    name="variants[{{ $rowKey }}][image]"
                    accept="image/*"
                    class="flex-1 text-xs text-ink/70 file:mr-3 file:rounded-md file:border-0 file:bg-ink file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-bone file:transition-colors hover:file:bg-ink/85"
                >
            </div>
        </div>

        <label class="flex items-center gap-2 pb-2 text-xs text-ink/70">
            <input
                type="checkbox"
                name="variants[{{ $rowKey }}][is_active]"
                value="1"
                @checked($variant?->isActive ?? true)
                class="h-4 w-4 rounded border-ink/30 text-ink focus:ring-ink/30"
            >
            Active
        </label>

        <button type="button" data-remove-variant class="pb-2 text-xs font-medium text-madder transition-colors hover:underline">
            Remove
        </button>
    </div>
</div>
