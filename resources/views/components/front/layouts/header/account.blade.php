@props(['dark' => false])

<a
    href="{{ auth()->check() ? route('front.account.profile') : route('front.login') }}"
    aria-label="{{ auth()->check() ? __('header.account') : __('header.login') }}"
    class="inline-flex items-center {{ $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink' }} transition-colors duration-200"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
    </svg>
</a>
