@php
    $benefits = [
        [
            'title' => 'Швидка доставка',
            'description' => 'Відправляємо протягом 1-2 днів по всій Україні.',
            'icon' => 'M3 3h2l.4 2M7 13h10l3.6-8H5.4M7 13L5.4 5M7 13l-1.6 4h12.2M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z',
        ],
        [
            'title' => 'Повернення протягом 14 днів',
            'description' => 'Якщо річ не підійшла — повернемо кошти без зайвих питань.',
            'icon' => 'M9 15L4 10l5-5M4 10h9a6 6 0 016 6v1',
        ],
        [
            'title' => 'Гарантія якості',
            'description' => 'Щільні натуральні тканини та ручне фарбування у кожній партії.',
            'icon' => 'M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z',
        ],
        [
            'title' => 'Онлайн підтримка',
            'description' => 'Відповідаємо на запитання про товар і замовлення щодня.',
            'icon' => 'M12 20.25c4.556 0 8.25-3.694 8.25-8.25S16.556 3.75 12 3.75 3.75 7.444 3.75 12c0 1.4.35 2.72.966 3.874L3.75 20.25l4.376-.966A8.213 8.213 0 0012 20.25z',
        ],
    ];
@endphp

<section class="mx-auto max-w-6xl px-4 py-16 md:px-10 md:py-20">
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($benefits as $benefit)
            <div class="text-center sm:text-left">
                <div class="mx-auto mb-4 flex h-11 w-11 items-center justify-center border border-stone text-ink sm:mx-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $benefit['icon'] }}" />
                    </svg>
                </div>
                <h3 class="mb-1.5 text-sm font-medium text-ink">{{ $benefit['title'] }}</h3>
                <p class="text-sm text-ink/60">{{ $benefit['description'] }}</p>
            </div>
        @endforeach
    </div>
</section>
