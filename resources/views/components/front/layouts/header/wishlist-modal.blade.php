@props([])

<div data-component="front/layouts/header/wishlist-modal" class="contents">
    <div
        data-wishlist-modal-backdrop
        class="pointer-events-none fixed inset-0 z-40 bg-ink/40 opacity-0 transition-opacity duration-300"
    ></div>

    <div
        id="wishlist-modal-panel"
        data-wishlist-modal-panel
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md translate-x-full flex-col overflow-y-auto bg-bone shadow-xl transition-transform duration-300"
    >
        <div class="flex items-center justify-between border-b border-ink/10 px-6 py-5">
            <span class="font-serif text-lg text-ink">Список бажань</span>
            <button type="button" data-wishlist-modal-close aria-label="{{ __('header.close_menu') }}" class="text-ink/60">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 px-6 py-6">
            @auth
                <div data-wishlist-modal-contents data-wishlist-fetch-url="{{ route('front.account.wishlist') }}">
                    <p class="py-12 text-center font-mono text-xs uppercase tracking-widest text-ink/40">Завантаження…</p>
                </div>
            @else
                <div class="py-12 text-center">
                    <p class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">Увійдіть, щоб переглянути список бажань</p>
                    <a
                        href="{{ route('front.login') }}"
                        class="font-mono text-xs uppercase tracking-widest text-ink underline decoration-dotted underline-offset-4 hover:text-madder"
                    >
                        Увійти →
                    </a>
                </div>
            @endauth
        </div>

        @auth
            <div class="border-t border-ink/10 px-6 py-4">
                <a
                    href="{{ route('front.account.wishlist') }}"
                    class="block text-center font-mono text-xs uppercase tracking-widest text-ink underline decoration-dotted underline-offset-4 hover:text-madder"
                >
                    Переглянути всі →
                </a>
            </div>
        @endauth
    </div>
</div>
