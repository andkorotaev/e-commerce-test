<x-admin.layouts.layout title="Attributes">
    <div class="mx-auto max-w-4xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Attributes</h1>
            <a href="{{ route('admin.product-attributes.create') }}" class="rounded-md bg-ink px-4 py-2 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
                New attribute
            </a>
        </div>

        @if ($attributes->isEmpty())
            <p class="text-sm text-ink/50">No attributes yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Slug</th>
                            <th class="px-4 py-3 font-medium">Values</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($attributes as $attribute)
                            <tr>
                                <td class="px-4 py-3">{{ $attribute->translation('uk')?->name ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-ink/60">{{ $attribute->slug }}</td>
                                <td class="px-4 py-3 text-ink/60">
                                    {{ $attribute->values->map(fn ($value) => $value->translation('uk')?->value)->filter()->implode(', ') ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.product-attributes.edit', $attribute->id) }}" class="text-ink/60 transition-colors hover:text-ink hover:underline">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.product-attributes.destroy', $attribute->id) }}" onsubmit="return confirm('Delete this attribute?')" class="inline">
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
