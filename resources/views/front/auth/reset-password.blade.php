<x-front.layouts.layout title="Новий пароль">
    <div class="mx-auto flex max-w-sm flex-col px-4 py-20">
        <h1 class="mb-8 font-serif text-3xl text-ink">Новий пароль</h1>

        @if ($errors->any())
            <div class="mb-6 border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('front.password.update') }}" class="flex flex-col gap-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    autofocus
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label for="password" class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Новий пароль</label>
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
                Зберегти новий пароль
            </button>
        </form>
    </div>
</x-front.layouts.layout>
