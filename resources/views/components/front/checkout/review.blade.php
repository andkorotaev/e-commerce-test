@props(['summary'])

<div class="h-fit space-y-6 border border-stone/30 p-6">
    <h2 class="font-mono text-xs uppercase tracking-widest text-ink/40">Ваше замовлення</h2>

    <div class="max-h-80 space-y-4 divide-y divide-stone/20 overflow-y-auto">
        @foreach ($summary->lines as $line)
            <div class="flex gap-3 pt-4 first:pt-0">
                <div class="h-16 w-14 shrink-0 overflow-hidden bg-stone/10">
                    @if ($line->image)
                        <img src="{{ Storage::url($line->image) }}" alt="{{ $line->name }}" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm text-ink">{{ $line->name }}</p>
                    @if ($line->variantLabel)
                        <p class="font-mono text-xs text-ink/40">{{ $line->variantLabel }}</p>
                    @endif
                    <p class="font-mono text-xs text-ink/50">{{ $line->quantity }} × {{ number_format($line->unitPrice, 0, ',', ' ') }} ₴</p>
                </div>
                <p class="font-mono text-sm text-ink">{{ number_format($line->lineTotal(), 0, ',', ' ') }} ₴</p>
            </div>
        @endforeach
    </div>

    <dl class="space-y-2 border-t border-stone/30 pt-4 text-sm">
        <div class="flex justify-between">
            <dt class="text-ink/60">Товари ({{ $summary->itemCount() }})</dt>
            <dd class="text-ink">{{ number_format($summary->subtotal, 0, ',', ' ') }} ₴</dd>
        </div>

        @if ($summary->discount > 0)
            <div class="flex justify-between">
                <dt class="text-ink/60">Знижка</dt>
                <dd class="text-madder">−{{ number_format($summary->discount, 0, ',', ' ') }} ₴</dd>
            </div>
        @endif

        <div class="flex justify-between">
            <dt class="text-ink/60">Доставка</dt>
            <dd class="text-ink">
                {{ $summary->delivery > 0 ? number_format($summary->delivery, 0, ',', ' ').' ₴' : 'Безкоштовно' }}
            </dd>
        </div>
    </dl>

    <div class="flex items-baseline justify-between border-t border-stone/30 pt-4 font-mono text-base text-ink">
        <span class="text-xs uppercase tracking-widest text-ink/40">Разом</span>
        <span>{{ number_format($summary->total, 0, ',', ' ') }} ₴</span>
    </div>

    <button
        type="submit"
        class="block w-full bg-ink px-6 py-3 text-center font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
    >
        Оформити замовлення
    </button>
</div>
