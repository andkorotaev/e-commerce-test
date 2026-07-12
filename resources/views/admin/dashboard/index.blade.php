<x-admin.layouts.layout title="Dashboard">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Dashboard</h1>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-ink/60 underline hover:text-ink">
                    Log out
                </button>
            </form>
        </div>

        <p class="font-mono text-xs uppercase tracking-widest text-ink/40">
            Logged in as {{ auth('admin')->user()->email }}
        </p>
    </div>
</x-admin.layouts.layout>
