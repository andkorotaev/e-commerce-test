@php
    $carriers = config('shop.delivery_carriers');
    $types = config('shop.delivery_types');
    $pointTypes = ['branch', 'postomat'];
@endphp

<div data-delivery-section>
    <h2 class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">Доставка</h2>

    <div class="mb-4 flex flex-wrap gap-3">
        @foreach ($carriers as $value => $label)
            <label class="flex-1 cursor-pointer">
                <input
                    type="radio"
                    name="delivery_carrier"
                    value="{{ $value }}"
                    data-delivery-carrier
                    @checked(old('delivery_carrier', array_key_first($carriers)) === $value)
                    class="peer sr-only"
                >
                <span class="block border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
    @error('delivery_carrier') <p class="mb-4 text-xs text-madder">{{ $message }}</p> @enderror

    <div class="mb-4 flex flex-wrap gap-3">
        @foreach ($types as $value => $label)
            <label class="flex-1 cursor-pointer">
                <input
                    type="radio"
                    name="delivery_type"
                    value="{{ $value }}"
                    data-delivery-type
                    data-needs-point="{{ in_array($value, $pointTypes) ? '1' : '0' }}"
                    @checked(old('delivery_type', array_key_first($types)) === $value)
                    class="peer sr-only"
                >
                <span class="block border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
    @error('delivery_type') <p class="mb-4 text-xs text-madder">{{ $message }}</p> @enderror

    <div data-delivery-point-wrapper>
        <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40" data-delivery-point-label>
            Номер відділення
        </label>
        <input
            type="text"
            name="delivery_point"
            value="{{ old('delivery_point') }}"
            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
        >
        @error('delivery_point') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
    </div>
</div>
