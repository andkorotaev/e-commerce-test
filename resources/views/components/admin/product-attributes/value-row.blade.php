@props(['rowKey', 'value' => null])

@php
    $inputClass = 'w-full rounded-md border border-ink/15 bg-white px-3 py-2 text-sm shadow-sm transition-colors focus:border-ink focus:outline-none focus:ring-1 focus:ring-ink/20';
    $labelClass = 'mb-1 block text-xs font-medium text-ink/60';
@endphp

<div data-value-row class="grid grid-cols-[repeat(auto-fit,minmax(140px,1fr))_auto] items-end gap-3 rounded-md border border-ink/10 p-4">
    @if ($value)
        <input type="hidden" name="values[{{ $rowKey }}][id]" value="{{ $value->id }}">
    @endif

    <div>
        <label class="{{ $labelClass }}">Slug</label>
        <input type="text" name="values[{{ $rowKey }}][slug]" value="{{ $value?->slug }}" class="{{ $inputClass }}">
    </div>

    @foreach (config('localization.locales') as $locale => $label)
        <div>
            <label class="{{ $labelClass }}">{{ $label }} <span class="font-mono text-[10px] uppercase text-ink/40">{{ $locale }}</span></label>
            <input
                type="text"
                name="values[{{ $rowKey }}][translations][{{ $locale }}][value]"
                value="{{ $value?->translation($locale)?->value }}"
                class="{{ $inputClass }}"
            >
        </div>
    @endforeach

    <button type="button" data-remove-value class="rounded-md px-3 py-2 text-xs font-medium text-madder transition-colors hover:bg-madder/10">
        Remove
    </button>
</div>
