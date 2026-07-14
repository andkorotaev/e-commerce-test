<x-front.layouts.layout title="Реєстрація">
    <div class="mx-auto flex max-w-sm flex-col px-4 py-20">
        <h1 class="mb-8 font-serif text-3xl text-ink">Реєстрація</h1>

        @if ($errors->any())
            <div class="mb-6 border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('front.register.store') }}" class="flex flex-col gap-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Ім'я</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label for="email" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
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
                    autocomplete="new-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">
                    Підтвердження пароля
                </label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <button type="submit" class="mt-2 bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder">
                Зареєструватися
            </button>
        </form>

        <div class="mt-6 font-mono text-xs text-ink/50">
            Вже маєте акаунт?
            <a href="{{ route('front.login') }}" class="underline decoration-dotted underline-offset-4 hover:text-ink">
                Увійти
            </a>
        </div>
    </div>
</x-front.layouts.layout>
