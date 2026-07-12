@props(['dark' => false])

@php
    $items = [
        ['label' => __('header.nav.menswear'), 'href' => '#'],
        ['label' => __('header.nav.womenswear'), 'href' => '#'],
        ['label' => __('header.nav.footwear'), 'href' => '#'],
        ['label' => __('header.nav.accessories'), 'href' => '#'],
    ];
    $color = $dark ? 'text-bone/75 hover:text-bone' : 'text-ink/70 hover:text-ink';
@endphp

<nav class="flex items-center gap-7 text-[13px] tracking-wide">
    @foreach ($items as $item)
        <a
            href="{{ $item['href'] }}"
            class="{{ $color }} border-b border-transparent pb-0.5 transition-colors duration-200 hover:border-current"
        >
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
