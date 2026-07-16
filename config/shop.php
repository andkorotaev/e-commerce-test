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

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    |
    | Delivery carriers and payment methods offered at checkout, keyed by
    | the value stored on the order. Fictional carrier/bank names (this is
    | a demo store, not integrated with any real logistics or payment API —
    | selecting one just records the choice, nothing is actually charged or
    | shipped).
    |
    */

    'delivery_carriers' => [
        'stara_poshta' => 'Стара Пошта',
        'bitan_poshta' => 'Брітан Пошта',
    ],

    'delivery_types' => [
        'branch' => 'У відділення',
        'postomat' => 'У поштомат',
        'address' => 'Кур\'єром за адресою',
    ],

    'payment_methods' => [
        'stereo_bank' => 'СтереоБанк',
        'public_bank' => 'ПаблікБанк',
    ],

    /*
    |--------------------------------------------------------------------------
    | Order statuses
    |--------------------------------------------------------------------------
    |
    | Every order is created as "new" — the others exist here as the label
    | map for whenever admin-side status management is built; not wiring up
    | that management yet doesn't mean the display labels shouldn't already
    | have somewhere honest to live.
    |
    */

    'order_statuses' => [
        'new' => 'Нове',
        'processing' => 'В обробці',
        'shipped' => 'Відправлено',
        'completed' => 'Виконано',
        'cancelled' => 'Скасовано',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact details
    |--------------------------------------------------------------------------
    |
    | The single source of truth for the store's public contact info — the
    | footer and the "Contacts" page both read from here, so the address/
    | phone/email shown in each can never drift apart. map_query is a plain
    | address string handed to Google's no-API-key embed endpoint
    | (google.com/maps?q=...&output=embed), not coordinates.
    |
    */

    'contact' => [
        'email' => 'hello@ocre.ua',
        'phone' => '+380441234567',
        'phone_display' => '+380 44 123 45 67',
        'address' => 'Київ, вул. Хрещатик, 1',
        'map_query' => 'Київ, вул. Хрещатик, 1',
    ],

];
