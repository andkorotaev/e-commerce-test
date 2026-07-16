@php $methods = config('shop.payment_methods'); @endphp

<div>
    <h2 class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">Оплата</h2>

    <div class="flex flex-wrap gap-3" data-payment-group>
        @foreach ($methods as $value => $label)
            <label class="flex-1 cursor-pointer">
                <input
                    type="checkbox"
                    name="payment_method"
                    value="{{ $value }}"
                    data-payment-checkbox
                    @checked(old('payment_method') === $value)
                    class="peer sr-only"
                >
                <span class="flex flex-col items-center gap-2 border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
    @error('payment_method') <p class="mt-2 text-xs text-madder">{{ $message }}</p> @enderror
</div>
