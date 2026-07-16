@php
    $carriers = config('shop.delivery_carriers');
    $types = config('shop.delivery_types');
    $pointTypes = ['branch', 'postomat'];

    $carrierIcons = [
        'stara_poshta' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
        'bitan_poshta' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
    ];

    $typeIcons = [
        'branch' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
        'postomat' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6.25 3.75h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        'address' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z|M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
    ];

    $cities = app(\App\Services\CityLookupService::class)->all();
@endphp

<div data-delivery-section data-delivery-points-url="{{ route('front.checkout.delivery-points') }}">
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
                <span class="flex flex-col items-center gap-2 border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $carrierIcons[$value] ?? $carrierIcons['stara_poshta'] }}" />
                    </svg>
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
                <span class="flex flex-col items-center gap-2 border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                        @foreach (explode('|', $typeIcons[$value] ?? $typeIcons['branch']) as $path)
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
                        @endforeach
                    </svg>
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
    @error('delivery_type') <p class="mb-4 text-xs text-madder">{{ $message }}</p> @enderror

    <div class="mb-4" data-city-select>
        <x-front.checkout.search-select
            name="city"
            label="Місто"
            placeholder="Почніть вводити назву міста..."
            :value="old('city')"
            :required="true"
            :options="$cities"
        />
    </div>

    <div data-delivery-point-wrapper>
        <x-front.checkout.search-select
            name="delivery_point"
            label="Номер відділення"
            placeholder="Спочатку оберіть місто..."
            :value="old('delivery_point')"
        />
    </div>

    <div data-delivery-address-wrapper class="hidden">
        <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Адреса</label>
        <input
            type="text"
            name="address"
            value="{{ old('address') }}"
            class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
        >
        @error('address') <p class="mt-1 text-xs text-madder">{{ $message }}</p> @enderror
    </div>
</div>
