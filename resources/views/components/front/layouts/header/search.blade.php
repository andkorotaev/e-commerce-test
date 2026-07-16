@props(['dark' => false])

<button
    type="button"
    data-search-trigger
    aria-label="{{ __('header.search') }}"
    class="{{ $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink' }} transition-colors duration-200"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-[18px] w-[18px]">
        <circle cx="11" cy="11" r="7" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </svg>
</button>
