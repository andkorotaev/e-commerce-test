@props(['summary'])

@if ($summary->lines->isEmpty())
    <div class="py-12">
        <p class="mb-6 font-mono text-xs uppercase tracking-widest text-ink/40">Кошик порожній</p>
        <a href="{{ route('front.home') }}" class="font-mono text-xs uppercase tracking-widest text-ink underline decoration-dotted underline-offset-4 hover:text-madder">
            До покупок →
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1fr_320px]">
        <div class="divide-y divide-stone/20">
            @foreach ($summary->lines as $line)
                <x-front.cart.item :line="$line" />
            @endforeach
        </div>

        <x-front.cart.summary :summary="$summary" />
    </div>
@endif
