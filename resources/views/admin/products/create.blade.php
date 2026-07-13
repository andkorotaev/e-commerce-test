<x-admin.layouts.layout title="New product">
    <div class="mx-auto max-w-3xl px-8 py-10">
        <a href="{{ route('admin.products.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Products
        </a>
        <h1 class="mb-8 text-lg font-medium text-ink">New product</h1>

        <x-admin.products.form :category-options="$categoryOptions" />
    </div>
</x-admin.layouts.layout>
