<x-admin.layouts.layout title="Log in">
    <div class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-sm rounded-sm border border-ink/10 bg-bone p-8">
            <h1 class="mb-6 text-lg font-medium text-ink">Admin login</h1>

            @if ($errors->any())
                <div class="mb-5 rounded-sm border border-madder/30 bg-madder/5 px-4 py-3 text-sm text-madder">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="flex flex-col gap-4">
                @csrf

                <div>
                    <label for="email" class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full border border-ink/15 bg-white px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                    >
                </div>

                <div>
                    <label for="password" class="mb-1 block text-xs uppercase tracking-wide text-ink/60">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-ink/15 bg-white px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-ink/70">
                    <input type="checkbox" name="remember" class="border-ink/30">
                    Remember me
                </label>

                <button
                    type="submit"
                    class="mt-2 bg-ink px-4 py-2.5 text-sm font-medium text-bone transition-colors hover:bg-ink/85"
                >
                    Log in
                </button>
            </form>
        </div>
    </div>
</x-admin.layouts.layout>
