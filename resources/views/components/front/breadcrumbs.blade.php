@props(['items'])

<nav aria-label="Хлібні крихти" class="mb-4 flex flex-wrap items-center gap-2 font-mono text-xs uppercase tracking-widest text-ink/40">
    @foreach ($items as $index => $item)
        @if (! $loop->first)
            <span aria-hidden="true">/</span>
        @endif

        @if ($item['url'] && ! $loop->last)
            <a href="{{ $item['url'] }}" class="transition-colors hover:text-ink">{{ $item['label'] }}</a>
        @else
            <span class="text-ink/70" @if (! $loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>

<script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->values()->map(fn ($item, $index) => array_filter([
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'],
            'item' => $item['url'] ? url($item['url']) : null,
        ]))->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
