<x-front.layouts.layout title="Реєстрація" description="Створіть акаунт OCRE, щоб зберігати список бажань та відстежувати замовлення.">
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
                <label for="first_name" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Ім'я</label>
                <input
                    id="first_name"
                    type="text"
                    name="first_name"
                    value="{{ old('first_name') }}"
                    required
                    autofocus
                    autocomplete="given-name"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label for="last_name" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">
                    Прізвище <span class="normal-case text-ink/30">(необов'язково)</span>
                </label>
                <input
                    id="last_name"
                    type="text"
                    name="last_name"
                    value="{{ old('last_name') }}"
                    autocomplete="family-name"
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
                <label for="phone" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Телефон</label>
                <input
                    id="phone"
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    autocomplete="tel"
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
