@props(['categories', 'dark' => false])

@php
    $color = $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink';
@endphp

<nav class="relative hidden items-center gap-7 text-[13px] tracking-wide lg:flex">
    @foreach ($categories as $category)
        @php $translation = $category->translation(app()->getLocale()); @endphp
        <div class="group">
            <a
                href="{{ route('front.categories.show', $translation->slug) }}"
                class="{{ $color }} border-b border-transparent pb-0.5 transition-colors duration-200 hover:border-current"
            >
                {{ $translation?->name }}
            </a>

            @if ($category->children->isNotEmpty())
                <div class="invisible absolute left-1/2 top-full z-20 w-[1040px] -translate-x-1/2 pt-4 opacity-0 transition-all duration-200 group-hover:visible group-hover:opacity-100">
                    <div class="flex gap-12 border border-ink/10 bg-bone p-10 shadow-lg">
                        @if ($category->image)
                            <div class="aspect-[3/4] w-56 shrink-0 overflow-hidden">
                                <img
                                    src="{{ Storage::url($category->image) }}"
                                    alt="{{ $translation?->name }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        @endif

                        <div class="grid flex-1 grid-cols-4 gap-10">
                            @foreach ($category->children as $level2)
                                @php $level2Translation = $level2->translation(app()->getLocale()); @endphp
                                <div>
                                    <a
                                        href="{{ route('front.categories.show', $level2Translation->slug) }}"
                                        class="mb-3 block text-sm font-medium text-ink transition-colors hover:text-madder"
                                    >
                                        {{ $level2Translation?->name }}
                                    </a>
                                    <ul class="flex flex-col gap-2">
                                        @foreach ($level2->children as $level3)
                                            @php $level3Translation = $level3->translation(app()->getLocale()); @endphp
                                            <li>
                                                <a
                                                    href="{{ route('front.categories.show', $level3Translation->slug) }}"
                                                    class="text-sm text-ink/60 transition-colors hover:text-ink"
                                                >
                                                    {{ $level3Translation?->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</nav>
