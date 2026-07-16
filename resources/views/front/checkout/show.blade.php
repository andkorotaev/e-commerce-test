<x-front.layouts.layout title="Оформлення замовлення">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-10">
        <h1 class="mb-10 font-serif text-3xl text-ink">Оформлення замовлення</h1>

        <x-front.checkout :summary="$summary" :prefill="$prefill" />
    </div>
</x-front.layouts.layout>
