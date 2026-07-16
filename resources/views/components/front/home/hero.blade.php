@php
    $slides = [
        [
            'image' => 'home/hero-1.jpg',
            'eyebrow' => 'Small-batch · Натуральні барвники',
            'headline' => 'Одяг, забарвлений землею',
            'description' => 'Індиго, волоський горіх, кошеніль і резеда — барвники, якими фарбували тканини задовго до нас. Щільні натуральні тканини, стримані силуети, речі на довгі роки.',
        ],
        [
            'image' => 'home/hero-2.jpg',
            'eyebrow' => 'Барвник №1 · Індиго',
            'headline' => 'Один барвник, безліч відтінків',
            'description' => 'Від глибокого нічного до вицвілого небесного — indigo з кожним праннях розкривається по-новому, залишаючись собою.',
        ],
        [
            'image' => 'home/hero-3.jpg',
            'eyebrow' => 'Кольори з рослин',
            'headline' => 'Відтінки, народжені землею',
            'description' => 'Резеда, волоський горіх і глина — палітра, в якій немає жодного синтетичного пігменту.',
        ],
    ];
@endphp

<section data-component="front/home/hero" class="relative h-[560px] w-full overflow-hidden md:h-[640px]">
    @foreach ($slides as $index => $slide)
        <div
            data-hero-slide
            data-index="{{ $index }}"
            class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'pointer-events-none opacity-0' }}"
        >
            <img
                src="{{ Storage::url($slide['image']) }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover object-[center_40%]"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-ink/80 via-ink/40 to-ink/5"></div>

            <div class="relative mx-auto flex h-full max-w-6xl items-center px-4 md:px-10">
                <div class="max-w-lg">
                    <p class="mb-4 font-mono text-xs uppercase tracking-widest text-madder">
                        {{ $slide['eyebrow'] }}
                    </p>
                    <h1 class="mb-6 font-serif text-4xl leading-[1.1] text-bone md:text-6xl">
                        {{ $slide['headline'] }}
                    </h1>
                    <p class="mb-9 max-w-md text-bone/70">
                        {{ $slide['description'] }}
                    </p>
                    <a
                        href="#categories"
                        class="inline-block bg-bone px-8 py-4 font-mono text-xs uppercase tracking-widest text-ink transition-colors hover:bg-madder hover:text-bone"
                    >
                        Перейти до каталогу
                    </a>
                </div>
            </div>
        </div>
    @endforeach

    @if (count($slides) > 1)
        <div class="absolute inset-x-0 bottom-6 z-10 flex justify-center gap-2 md:bottom-10 md:justify-start md:pl-10">
            @foreach ($slides as $index => $slide)
                <button
                    type="button"
                    data-hero-dot
                    data-index="{{ $index }}"
                    @if ($index === 0) data-active="true" @endif
                    aria-label="Слайд {{ $index + 1 }}"
                    class="h-1.5 w-8 bg-bone/40 transition-colors data-[active=true]:bg-bone"
                ></button>
            @endforeach
        </div>
    @endif
</section>
