<x-admin.layouts.layout title="Reviews">
    <div class="mx-auto max-w-5xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Reviews</h1>
        </div>

        @if ($reviews->isEmpty())
            <p class="text-sm text-ink/50">No reviews yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Product</th>
                            <th class="px-4 py-3 font-medium">Author</th>
                            <th class="px-4 py-3 font-medium">Rating</th>
                            <th class="px-4 py-3 font-medium">Comment</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($reviews as $review)
                            <tr>
                                <td class="px-4 py-3 text-ink/70">{{ $review->productName ?? '—' }}</td>
                                <td class="px-4 py-3 text-ink">{{ $review->authorName }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-ink/60">{{ $review->rating }} / 5</td>
                                <td class="max-w-sm truncate px-4 py-3 text-ink/60" title="{{ $review->comment }}">
                                    {{ $review->comment }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $review->isApproved ? 'Approved' : 'Pending' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @unless ($review->isApproved)
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-ink/60 transition-colors hover:text-ink hover:underline">Approve</button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Delete this review?')" class="inline">
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
