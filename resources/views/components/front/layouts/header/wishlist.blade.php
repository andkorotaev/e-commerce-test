@props(['dark' => false, 'count' => 0])

<a
    href="{{ auth()->check() ? route('front.account.wishlist') : route('front.login') }}"
    data-wishlist-trigger
    aria-label="{{ __('header.wishlist') }}"
    class="relative inline-flex items-center {{ $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink' }} transition-colors duration-200"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.35-9.5-8.5C.5 8 2 4.5 5.5 4c2-.3 3.7.7 4.5 2 .8-1.3 2.5-2.3 4.5-2 3.5.5 5 4 3 7.5C19 15.65 12 20 12 20z" />
    </svg>
    @if ($count > 0)
        <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-madder font-mono text-[10px] text-bone">
            {{ $count }}
        </span>
    @endif
</a>
