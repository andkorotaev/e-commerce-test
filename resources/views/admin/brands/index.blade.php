<x-admin.layouts.layout title="Brands">
    <div class="mx-auto max-w-4xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Brands</h1>
            <a href="{{ route('admin.brands.create') }}" class="rounded-md bg-ink px-4 py-2 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
                New brand
            </a>
        </div>

        @if ($brands->isEmpty())
            <p class="text-sm text-ink/50">No brands yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium"></th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Slug</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($brands as $brand)
                            <tr>
                                <td class="px-4 py-3">
                                    @if ($brand->logo)
                                        <img src="{{ Storage::url($brand->logo) }}" class="h-10 w-10 rounded-md object-cover" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-stone/20"></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 {{ $brand->isActive ? '' : 'text-ink/40 line-through' }}">
                                    {{ $brand->name }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-ink/60">{{ $brand->slug }}</td>
                                <td class="px-4 py-3 text-xs text-ink/50">
                                    {{ $brand->isActive ? 'Active' : 'Inactive' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-ink/60 transition-colors hover:text-ink hover:underline">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('Delete this brand?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-madder transition-colors hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin.layouts.layout>
