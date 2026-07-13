@props(['categories'])

<div data-component="front/layouts/header/mobile-menu" class="contents">
    <button
        type="button"
        data-mobile-menu-trigger
        aria-expanded="false"
        aria-controls="mobile-menu-panel"
        aria-label="{{ __('header.menu') }}"
        class="text-ink/80 lg:hidden"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div
        data-mobile-menu-backdrop
        class="pointer-events-none fixed inset-0 z-20 bg-ink/40 opacity-0 transition-opacity duration-300 lg:hidden"
    ></div>

    <div
        id="mobile-menu-panel"
        data-mobile-menu-panel
        class="fixed inset-y-0 right-0 z-30 w-full max-w-xs translate-x-full overflow-y-auto bg-bone shadow-xl transition-transform duration-300 lg:hidden"
    >
        <div class="flex items-center justify-between border-b border-ink/10 px-6 py-5">
            <span class="font-serif text-lg text-ink">{{ __('header.menu') }}</span>
            <button type="button" data-mobile-menu-close aria-label="{{ __('header.close_menu') }}" class="text-ink/60">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="px-6 py-4">
            @foreach ($categories as $root)
                @php $rootTranslation = $root->translation(app()->getLocale()); @endphp
                <div class="border-b border-ink/10 py-3 last:border-b-0">
                    <a
                        href="{{ route('front.categories.show', $rootTranslation->slug) }}"
                        class="block text-sm font-medium text-ink"
                    >
                        {{ $rootTranslation?->name }}
                    </a>

                    @if ($root->children->isNotEmpty())
                        <div class="mt-2 flex flex-col gap-2 pl-3">
                            @foreach ($root->children as $level2)
                                @php $level2Translation = $level2->translation(app()->getLocale()); @endphp
                                <div>
                                    <a
                                        href="{{ route('front.categories.show', $level2Translation->slug) }}"
                                        class="block text-sm text-ink/70"
                                    >
                                        {{ $level2Translation?->name }}
                                    </a>

                                    @if ($level2->children->isNotEmpty())
                                        <div class="mt-1 flex flex-col gap-1 pl-3">
                                            @foreach ($level2->children as $level3)
                                                @php $level3Translation = $level3->translation(app()->getLocale()); @endphp
                                                <a
                                                    href="{{ route('front.categories.show', $level3Translation->slug) }}"
                                                    class="block text-xs text-ink/50"
                                                >
                                                    {{ $level3Translation?->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </div>
</div>
