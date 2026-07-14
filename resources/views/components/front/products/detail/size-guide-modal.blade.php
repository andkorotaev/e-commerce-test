@props(['sizeValues'])

@php
    $isFootwear = $sizeValues->contains(fn ($value) => str_starts_with($value->slug, 'eu-'));

    $footwearRows = [
        ['38', '5', '6', '24.5'],
        ['39', '5.5', '6.5', '25'],
        ['40', '6.5', '7.5', '25.5'],
        ['41', '7', '8', '26.5'],
        ['42', '8', '9', '27'],
        ['43', '9', '10', '27.5'],
        ['44', '9.5', '10.5', '28.5'],
        ['45', '10.5', '11.5', '29'],
    ];

    $apparelRows = [
        ['XS', '84-88', '66-70'],
        ['S', '88-92', '70-74'],
        ['M', '92-96', '74-78'],
        ['L', '96-102', '78-84'],
        ['XL', '102-108', '84-90'],
        ['XXL', '108-114', '90-96'],
    ];
@endphp

<div data-component="front/products/detail/size-guide-modal" class="contents">
    <div
        data-size-guide-backdrop
        class="pointer-events-none fixed inset-0 z-40 bg-ink/50 opacity-0 transition-opacity duration-300"
    ></div>

    <div
        data-size-guide-panel
        class="pointer-events-none fixed left-1/2 top-1/2 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 bg-bone p-6 opacity-0 shadow-xl transition-opacity duration-300"
    >
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-serif text-xl text-ink">Таблиця розмірів</h3>
            <button type="button" data-size-guide-close class="text-ink/40 transition-colors hover:text-ink" aria-label="Закрити">
                ✕
            </button>
        </div>

        <p class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">
            {{ $isFootwear ? 'Орієнтовна відповідність розмірів взуття' : 'Орієнтовна відповідність розмірів одягу' }}
        </p>

        @if ($isFootwear)
            <table class="w-full font-mono text-sm text-ink/70">
                <thead>
                    <tr class="border-b border-stone/40 text-left text-xs uppercase tracking-widest text-ink/40">
                        <th class="py-2">EU</th>
                        <th class="py-2">UK</th>
                        <th class="py-2">US</th>
                        <th class="py-2">см</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($footwearRows as $row)
                        <tr class="border-b border-stone/20">
                            @foreach ($row as $cell)
                                <td class="py-2">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="w-full font-mono text-sm text-ink/70">
                <thead>
                    <tr class="border-b border-stone/40 text-left text-xs uppercase tracking-widest text-ink/40">
                        <th class="py-2">Розмір</th>
                        <th class="py-2">Груди, см</th>
                        <th class="py-2">Талія, см</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($apparelRows as $row)
                        <tr class="border-b border-stone/20">
                            @foreach ($row as $cell)
                                <td class="py-2">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
