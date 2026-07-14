<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shipping
    |--------------------------------------------------------------------------
    |
    | The header's announcement bar already advertises free shipping over a
    | threshold (the "shipping_notice" key in the header lang files) — this
    | is the single source of truth for that number, so the cart's actual
    | delivery-fee calculation and the announcement bar copy can never
    | drift apart. Below the threshold, a flat fee applies.
    |
    */

    'free_shipping_threshold' => 6000,

    'flat_shipping_fee' => 150,

    /*
    |--------------------------------------------------------------------------
    | Guest cart cookie
    |--------------------------------------------------------------------------
    |
    | How long an anonymous visitor's cart survives in their browser before
    | it's forgotten. Minutes, matching Laravel's Cookie::queue() signature.
    |
    */

    'guest_cart_cookie_minutes' => 60 * 24 * 30,

];
