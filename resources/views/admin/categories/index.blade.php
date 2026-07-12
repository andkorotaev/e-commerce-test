<x-admin.layouts.layout title="Categories">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Categories</h1>
            <a href="{{ route('admin.categories.create') }}" class="bg-ink px-4 py-2 text-sm font-medium text-bone hover:bg-ink/85">
                New category
            </a>
        </div>

        @if ($tree->isEmpty())
            <p class="text-sm text-ink/50">No categories yet.</p>
        @else
            <x-admin.categories.tree :categories="$tree" />
        @endif
    </div>
</x-admin.layouts.layout>
