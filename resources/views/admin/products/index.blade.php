<x-admin.layouts.layout title="Products">
    <div class="mx-auto max-w-5xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Products</h1>
            <a href="{{ route('admin.products.create') }}" class="rounded-md bg-ink px-4 py-2 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
                New product
            </a>
        </div>

        @if ($products->isEmpty())
            <p class="text-sm text-ink/50">No products yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium"></th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Price</th>
                            <th class="px-4 py-3 font-medium">Stock</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($products as $product)
                            @php
                                $translation = $product->translation('uk');
                                $primaryImage = $product->primaryImage();
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    @if ($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->path) }}" class="h-10 w-10 rounded-md object-cover" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-stone/20"></div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 {{ $product->isActive ? '' : 'text-ink/40 line-through' }}">
                                    {{ $translation?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-ink/60">{{ $product->sku ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    {{ number_format($product->price, 2) }}
                                    @if ($product->oldPrice)
                                        <span class="ml-1 text-xs text-ink/40 line-through">{{ number_format($product->oldPrice, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $product->stock }}</td>
                                <td class="px-4 py-3 text-xs text-ink/50">
                                    {{ $product->isActive ? 'Active' : 'Inactive' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-ink/60 transition-colors hover:text-ink hover:underline">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Delete this product?')" class="inline">
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
