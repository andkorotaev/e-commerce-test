<x-front.layouts.layout title="Сторінку не знайдено" description="Сторінку, яку ви шукали, не знайдено на сайті OCRE.">
    <div class="mx-auto flex max-w-lg flex-col items-center px-4 py-24 text-center">
        <p class="font-mono text-xs uppercase tracking-widest text-stone">Помилка 404</p>
        <h1 class="mt-3 font-serif text-4xl text-ink">Сторінку не знайдено</h1>
        <p class="mt-4 text-sm text-ink/60">
            Можливо, вона була видалена, перенесена, або посилання введено неправильно.
        </p>

        <div class="mt-10 flex flex-col gap-3 sm:flex-row">
            <a
                href="{{ route('front.home') }}"
                class="bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
            >
                На головну
            </a>
            <a
                href="{{ route('front.search') }}"
                class="border border-stone px-6 py-3 font-mono text-xs uppercase tracking-widest text-ink/70 transition-colors hover:border-ink hover:text-ink"
            >
                Пошук товарів
            </a>
        </div>
    </div>
</x-front.layouts.layout>
