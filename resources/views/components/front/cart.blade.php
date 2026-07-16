@props(['summary' => null, 'fetchUrl' => null])

<div
    data-component="front/cart"
    data-cart-add-url="{{ route('front.cart.add') }}"
    data-cart-update-url="{{ route('front.cart.update') }}"
    data-cart-remove-url="{{ route('front.cart.remove') }}"
    @if ($fetchUrl) data-cart-fetch-url="{{ $fetchUrl }}" @endif
>
    <div data-cart-contents>
        @if ($summary)
            <x-front.cart.contents :summary="$summary" />
        @else
            <p class="py-12 text-center font-mono text-xs uppercase tracking-widest text-ink/40">Завантаження…</p>
        @endif
    </div>
</div>
