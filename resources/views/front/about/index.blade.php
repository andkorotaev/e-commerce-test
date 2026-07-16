<x-front.layouts.layout
    title="Про магазин"
    description="Історія, місія та переваги OCRE — одягу малих партій, забарвленого натуральними барвниками."
    image="home/hero-1.jpg"
>
    <section class="mx-auto max-w-3xl px-4 py-16 text-center md:px-10 md:py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-stone">Про магазин</p>
        <h1 class="mt-3 font-serif text-4xl text-ink md:text-5xl">OCRE — одяг, забарвлений землею</h1>
        <p class="mt-6 text-ink/70">
            Малі партії, натуральні барвники — індиго, волоський горіх, кошеніль, резеда. Ми віримо, що речі,
            зроблені повільно й уважно, служать довше і носяться краще за швидку моду.
        </p>
    </section>

    <section class="bg-stone/10 py-16 md:py-20">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 px-4 md:grid-cols-2 md:px-10">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-stone">Історія</p>
                <h2 class="mt-2 font-serif text-2xl text-ink">Як усе починалось</h2>
                <p class="mt-4 text-sm leading-6 text-ink/70">
                    OCRE народився у невеликій майстерні, де перші светри фарбували вручну у волоському горісі та
                    індиго просто на кухні. Що починалось як експеримент із забутими рослинними барвниками, за
                    кілька років переросло у власне виробництво — з тими самими принципами: без поспіху, без
                    синтетичних пігментів, без масового виробництва. Кожна партія й досі обмежена — не тому, що так
                    зручніше, а тому, що ручне фарбування просто не масштабується без втрати якості.
                </p>
            </div>

            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-stone">Місія</p>
                <h2 class="mt-2 font-serif text-2xl text-ink">Навіщо ми це робимо</h2>
                <p class="mt-4 text-sm leading-6 text-ink/70">
                    Наша місія — повернути одягу зв'язок із матеріалом, з якого він зроблений. Ми фарбуємо тканини
                    пігментами, якими користувались задовго до нас — землею, корою, комахами, квітами — і робимо це
                    відкрито, без обіцянок ідеальної однорідності кольору, бо саме в цій неідеальності і є цінність
                    натурального. Ми хочемо, щоб речі носили роками, а не сезон.
                </p>
            </div>
        </div>
    </section>

    <x-front.benefits />

    <section class="mx-auto max-w-6xl px-4 py-16 md:px-10 md:py-20">
        <div class="mb-10">
            <p class="font-mono text-xs uppercase tracking-widest text-stone">Фотографії</p>
            <h2 class="mt-2 font-serif text-3xl text-ink">З майстерні та зйомок</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="aspect-[3/4] overflow-hidden bg-stone/10 sm:col-span-2 sm:row-span-2 sm:aspect-auto sm:h-full">
                <img src="{{ Storage::url('home/hero-1.jpg') }}" alt="Одяг на вішаку" class="h-full w-full object-cover">
            </div>
            <div class="aspect-square overflow-hidden bg-stone/10">
                <img src="{{ Storage::url('home/hero-2.jpg') }}" alt="Натуральні барвники" class="h-full w-full object-cover">
            </div>
            <div class="aspect-square overflow-hidden bg-stone/10">
                <img src="{{ Storage::url('home/hero-3.jpg') }}" alt="Тканини ручного фарбування" class="h-full w-full object-cover">
            </div>
        </div>
    </section>
</x-front.layouts.layout>
