<x-admin.layouts.layout title="New brand">
    <div class="mx-auto max-w-2xl px-8 py-10">
        <a href="{{ route('admin.brands.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Brands
        </a>
        <h1 class="mb-8 text-lg font-medium text-ink">New brand</h1>

        <x-admin.brands.form />
    </div>
</x-admin.layouts.layout>
