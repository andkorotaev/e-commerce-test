@props(['dark' => false, 'count' => 0])

<a
    href="#"
    aria-label="{{ __('header.cart') }}"
    class="relative inline-flex items-center {{ $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink' }} transition-colors duration-200"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l3.6-8H5.4M7 13L5.4 5M7 13l-1.6 4h12.2M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
    </svg>
    @if ($count > 0)
        <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-madder font-mono text-[10px] text-bone">
            {{ $count }}
        </span>
    @endif
</a>
