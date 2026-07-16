@props(['dark' => false, 'count' => 0])

<a
    href="{{ auth()->check() ? route('front.account.wishlist') : route('front.login') }}"
    data-wishlist-trigger
    aria-label="{{ __('header.wishlist') }}"
    class="relative inline-flex items-center {{ $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink' }} transition-colors duration-200"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
    </svg>
    @if ($count > 0)
        <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-madder font-mono text-[10px] text-bone">
            {{ $count }}
        </span>
    @endif
</a>
