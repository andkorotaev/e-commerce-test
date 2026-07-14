<x-front.layouts.layout title="Оформлення замовлення">
    <div class="mx-auto flex max-w-md flex-col items-center px-4 py-24 text-center">
        <h1 class="mb-4 font-serif text-3xl text-ink">Оформлення замовлення незабаром</h1>
        <p class="mb-8 text-sm text-ink/60">
            Ми ще працюємо над цією сторінкою. Ваш кошик нікуди не зникне — повертайтеся, коли будете готові.
        </p>
        <a
            href="{{ route('front.cart.show') }}"
            class="bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
        >
            ← Повернутися в кошик
        </a>
    </div>
</x-front.layouts.layout>
