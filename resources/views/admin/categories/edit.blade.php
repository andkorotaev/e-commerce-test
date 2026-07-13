<x-admin.layouts.layout title="Edit category">
    <div class="mx-auto max-w-3xl px-8 py-10">
        <a href="{{ route('admin.categories.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Categories
        </a>
        <h1 class="mb-8 text-lg font-medium text-ink">Edit category</h1>

        <x-admin.categories.form :category="$category" :parent-options="$parentOptions" />
    </div>
</x-admin.layouts.layout>
