<x-admin.layouts.layout title="Edit product">
    <div class="mx-auto max-w-3xl px-8 py-10">
        <a href="{{ route('admin.products.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Products
        </a>
        <h1 class="mb-8 text-lg font-medium text-ink">Edit product</h1>

        <x-admin.products.form :product="$product" :category-options="$categoryOptions" :brands="$brands" :product-attributes="$attributes" />
    </div>
</x-admin.layouts.layout>
