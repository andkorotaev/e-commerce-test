<x-front.layouts.layout title="Помилка сервера" description="На сайті OCRE сталася технічна помилка.">
    <div class="mx-auto flex max-w-lg flex-col items-center px-4 py-24 text-center">
        <p class="font-mono text-xs uppercase tracking-widest text-stone">Помилка 500</p>
        <h1 class="mt-3 font-serif text-4xl text-ink">Щось пішло не так</h1>
        <p class="mt-4 text-sm text-ink/60">
            На нашому боці сталася технічна помилка. Спробуйте оновити сторінку трохи пізніше.
        </p>

        <a
            href="{{ route('front.home') }}"
            class="mt-10 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder"
        >
            На головну
        </a>
    </div>
</x-front.layouts.layout>
