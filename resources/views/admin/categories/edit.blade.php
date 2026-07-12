<x-admin.layouts.layout title="Edit category">
    <div class="mx-auto max-w-3xl px-6 py-10">
        <h1 class="mb-8 text-lg font-medium text-ink">Edit category</h1>

        <x-admin.categories.form :category="$category" :parent-options="$parentOptions" />
    </div>
</x-admin.layouts.layout>
