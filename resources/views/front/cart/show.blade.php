<x-front.layouts.layout title="Кошик" description="Перегляньте товари у вашому кошику та оформіть замовлення в OCRE.">
    <div class="mx-auto max-w-6xl px-4 py-12 md:px-10">
        <h1 class="mb-10 font-serif text-3xl text-ink">Кошик</h1>

        <x-front.cart :summary="$summary" />
    </div>
</x-front.layouts.layout>
