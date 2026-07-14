@props(['summary'])

<div
    data-component="front/cart"
    data-cart-add-url="{{ route('front.cart.add') }}"
    data-cart-update-url="{{ route('front.cart.update') }}"
    data-cart-remove-url="{{ route('front.cart.remove') }}"
>
    <div data-cart-contents>
        <x-front.cart.contents :summary="$summary" />
    </div>
</div>
