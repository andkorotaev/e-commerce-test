@props(['title'])

<div class="mx-auto max-w-5xl px-4 py-12 md:px-10">
    <h1 class="mb-10 font-serif text-3xl text-ink">Особистий кабінет</h1>

    <div class="grid grid-cols-1 gap-10 md:grid-cols-[200px_1fr]">
        <x-front.account.nav />

        <div class="min-w-0">
            <h2 class="mb-6 font-mono text-xs uppercase tracking-widest text-ink/40">{{ $title }}</h2>
            {{ $slot }}
        </div>
    </div>
</div>
