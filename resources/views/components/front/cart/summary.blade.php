@props(['summary'])

<div class="h-fit space-y-4 border border-stone/30 p-6">
    <h2 class="font-mono text-xs uppercase tracking-widest text-ink/40">Підсумок замовлення</h2>

    <dl class="space-y-2 text-sm">
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

    <a
        href="{{ route('front.checkout') }}"
        class="block bg-ink px-6 py-3 text-center font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
    >
        Оформити замовлення
    </a>
</div>
