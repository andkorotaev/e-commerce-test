@php
    $links = [
        ['label' => 'Профіль', 'route' => 'front.account.profile', 'active' => request()->routeIs('front.account.profile')],
        ['label' => 'Історія замовлень', 'route' => 'front.account.orders', 'active' => request()->routeIs('front.account.orders')],
        ['label' => 'Список бажань', 'route' => 'front.account.wishlist', 'active' => request()->routeIs('front.account.wishlist')],
    ];
@endphp

<nav class="flex flex-row gap-1 overflow-x-auto md:flex-col">
    @foreach ($links as $link)
        <a
            href="{{ route($link['route']) }}"
            class="whitespace-nowrap px-4 py-2 font-mono text-xs uppercase tracking-widest transition-colors {{ $link['active'] ? 'bg-ink text-bone' : 'text-ink/60 hover:bg-stone/10 hover:text-ink' }}"
        >
            {{ $link['label'] }}
        </a>
    @endforeach

    <form method="POST" action="{{ route('front.logout') }}" class="md:mt-4">
        @csrf
        <button type="submit" class="w-full whitespace-nowrap px-4 py-2 text-left font-mono text-xs uppercase tracking-widest text-ink/60 transition-colors hover:bg-stone/10 hover:text-ink">
            Вийти
        </button>
    </form>
</nav>
