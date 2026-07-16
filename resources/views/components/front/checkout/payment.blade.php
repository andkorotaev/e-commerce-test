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
                <span class="block border border-stone px-4 py-3 text-center text-sm text-ink/70 peer-checked:border-ink peer-checked:bg-ink peer-checked:text-bone">
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
    @error('payment_method') <p class="mt-2 text-xs text-madder">{{ $message }}</p> @enderror
</div>
