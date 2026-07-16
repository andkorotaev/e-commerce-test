<x-front.layouts.layout title="Вхід" description="Увійдіть до свого акаунту OCRE.">
    <div class="mx-auto flex max-w-sm flex-col px-4 py-20">
        <h1 class="mb-8 font-serif text-3xl text-ink">Вхід</h1>

        @if ($errors->any())
            <div class="mb-6 border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 bg-stone/10 px-4 py-3 text-sm text-ink/70">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('front.login.store') }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label for="password" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Пароль</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <label class="flex items-center gap-2 text-sm text-ink/60">
                <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded-none border-stone text-madder focus:ring-madder">
                Запам'ятати мене
            </label>

            <button type="submit" class="mt-2 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder">
                Увійти
            </button>
        </form>

        <div class="mt-6 flex items-center justify-between font-mono text-xs text-ink/50">
            <a href="{{ route('front.password.request') }}" class="underline decoration-dotted underline-offset-4 hover:text-ink">
                Забули пароль?
            </a>
            <a href="{{ route('front.register') }}" class="underline decoration-dotted underline-offset-4 hover:text-ink">
                Реєстрація
            </a>
        </div>
    </div>
</x-front.layouts.layout>
