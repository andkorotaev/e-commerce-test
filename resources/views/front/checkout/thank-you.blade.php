<x-front.layouts.layout title="Дякуємо за замовлення">
    <div class="mx-auto flex max-w-lg flex-col items-center px-4 py-24 text-center">
        <h1 class="mb-4 font-serif text-4xl text-ink">Дякуємо за замовлення!</h1>
        <p class="mb-2 text-sm text-ink/60">Номер вашого замовлення</p>
        <p class="mb-8 font-mono text-lg text-ink">{{ $order->orderNumber() }}</p>

        <div class="mb-10 w-full border border-stone/30 p-6 text-left">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-ink/60">Отримувач</dt>
                    <dd class="text-ink">{{ $order->firstName }} {{ $order->lastName }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink/60">Доставка</dt>
                    <dd class="text-ink">{{ $order->deliveryCarrierLabel() }}, {{ $order->deliveryTypeLabel() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink/60">Оплата</dt>
                    <dd class="text-ink">{{ $order->paymentMethodLabel() }}</dd>
                </div>
                <div class="flex items-baseline justify-between border-t border-stone/20 pt-3 font-mono">
                    <dt class="text-xs uppercase tracking-widest text-ink/40">Разом</dt>
                    <dd class="text-base text-ink">{{ number_format($order->total, 0, ',', ' ') }} ₴</dd>
                </div>
            </dl>
        </div>

        @guest
            <p class="mb-4 text-sm text-ink/60">
                Створіть акаунт, щоб відстежувати статус замовлення та зберігати історію покупок.
            </p>
            <a
                href="{{ route('front.register') }}"
                class="mb-8 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
            >
                Зареєструватися
            </a>
        @endguest

        <a
            href="{{ route('front.home') }}"
            class="font-mono text-xs uppercase tracking-widest text-ink/50 underline decoration-dotted underline-offset-4 hover:text-ink"
        >
            ← На головну
        </a>
    </div>
</x-front.layouts.layout>
