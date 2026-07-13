<x-admin.layouts.layout title="Dashboard">
    <div class="mx-auto max-w-4xl px-8 py-10">
        <h1 class="mb-2 text-lg font-medium text-ink">Dashboard</h1>
        <p class="text-sm text-ink/50">
            Logged in as {{ auth('admin')->user()->email }}
        </p>
    </div>
</x-admin.layouts.layout>
