<x-admin.layouts.layout title="Edit attribute">
    <div class="mx-auto max-w-3xl px-8 py-10">
        <a href="{{ route('admin.product-attributes.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Attributes
        </a>
        <h1 class="mb-8 text-lg font-medium text-ink">Edit attribute</h1>

        <x-admin.product-attributes.form :attribute="$attribute" />
    </div>
</x-admin.layouts.layout>
