<x-front.layouts.layout title="Профіль">
    <x-front.account.layout title="Профіль">
        @if (session('status') === 'profile-updated')
            <p class="mb-6 bg-stone/10 px-4 py-3 text-sm text-ink/70">Дані оновлено.</p>
        @endif

        @if ($errors->any())
            <div class="mb-6 border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('front.account.profile.update') }}" class="flex max-w-md flex-col gap-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Ім'я</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <hr class="my-2 border-stone/30">

            <p class="font-mono text-xs uppercase tracking-widest text-ink/40">Змінити пароль (необов'язково)</p>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Поточний пароль</label>
                <input
                    type="password"
                    name="current_password"
                    autocomplete="current-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">Новий пароль</label>
                <input
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block font-mono text-xs uppercase tracking-widest text-ink/40">
                    Підтвердження нового пароля
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full border border-stone bg-transparent px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                >
            </div>

            <button type="submit" class="mt-2 self-start bg-ink px-6 py-3 font-mono text-xs uppercase tracking-widest text-bone transition-colors hover:bg-madder">
                Зберегти
            </button>
        </form>
    </x-front.account.layout>
</x-front.layouts.layout>
