@props(['name', 'label', 'placeholder' => 'Почніть вводити для пошуку...', 'value' => null, 'required' => false, 'options' => null])

<div data-search-select class="relative">
    <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">{{ $label }}</label>
    <input
        type="text"
        name="{{ $name }}"
        data-search-select-input
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        @if ($required) required @endif
        @if ($options !== null) data-options="{{ json_encode(collect($options)->values()) }}" @endif
        class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
    >
    <ul data-search-select-list class="absolute z-20 mt-1 hidden max-h-56 w-full overflow-y-auto border border-stone bg-bone text-sm shadow-lg"></ul>
    @error($name) <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
</div>
