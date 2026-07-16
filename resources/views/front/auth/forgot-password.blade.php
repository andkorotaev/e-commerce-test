<x-front.layouts.layout title="Відновлення пароля" description="Відновіть доступ до вашого акаунту OCRE.">
    <div class="mx-auto flex max-w-sm flex-col px-4 py-20">
        <h1 class="mb-4 font-serif text-3xl text-ink">Відновлення пароля</h1>
        <p class="mb-8 text-sm text-ink/60">
            Введіть email, і ми надішлемо посилання для встановлення нового пароля.
        </p>

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

        <form method="POST" action="{{ route('front.password.email') }}" class="flex flex-col gap-4">
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
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <button type="submit" class="mt-2 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder">
                Надіслати посилання
            </button>
        </form>

        <div class="mt-6 font-mono text-xs text-ink/50">
            <a href="{{ route('front.login') }}" class="underline decoration-dotted underline-offset-4 hover:text-ink">
                ← Повернутися до входу
            </a>
        </div>
    </div>
</x-front.layouts.layout>
