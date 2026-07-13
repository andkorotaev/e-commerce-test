@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'active' => request()->routeIs('admin.categories.*')],
    ];
@endphp

<aside class="flex w-56 shrink-0 flex-col border-r border-ink/10 bg-white">
    <div class="px-5 py-6">
        <span class="font-mono text-xs uppercase tracking-widest text-ink/40">OCRE Admin</span>
    </div>

    <nav class="flex flex-1 flex-col gap-1 px-3">
        @foreach ($links as $link)
            <a
                href="{{ route($link['route']) }}"
                class="rounded-md px-3 py-2 text-sm transition-colors {{ $link['active'] ? 'bg-ink text-bone' : 'text-ink/70 hover:bg-ink/5 hover:text-ink' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-ink/10 px-3 py-4">
        <p class="mb-2 truncate px-3 font-mono text-xs text-ink/40">{{ auth('admin')->user()?->email }}</p>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm text-ink/70 transition-colors hover:bg-ink/5 hover:text-ink">
                Log out
            </button>
        </form>
    </div>
</aside>
