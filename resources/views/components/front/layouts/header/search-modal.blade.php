@props([])

<div data-component="front/layouts/header/search-modal" class="contents">
    <div
        data-search-modal-backdrop
        class="pointer-events-none fixed inset-0 z-40 bg-ink/40 opacity-0 transition-opacity duration-300"
    ></div>

    <div
        data-search-modal-panel
        class="fixed inset-x-0 top-0 z-50 -translate-y-full bg-bone shadow-xl transition-transform duration-300"
    >
        <form method="GET" action="{{ route('front.search') }}" class="mx-auto flex max-w-3xl items-center gap-4 px-4 py-8 md:px-10">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5 shrink-0 text-ink/40">
                <circle cx="11" cy="11" r="7" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>

            <input
                type="search"
                name="q"
                data-search-modal-input
                placeholder="Пошук товарів…"
                autocomplete="off"
                class="flex-1 border-b border-stone bg-transparent py-2 font-serif text-xl text-ink placeholder:text-ink/30 focus:border-ink focus:outline-none"
            >

            <button
                type="submit"
                class="shrink-0 font-mono text-xs uppercase tracking-widest text-ink underline decoration-dotted underline-offset-4 hover:text-madder"
            >
                Знайти
            </button>

            <button type="button" data-search-modal-close aria-label="{{ __('header.close_menu') }}" class="shrink-0 text-ink/60">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </form>
    </div>
</div>
