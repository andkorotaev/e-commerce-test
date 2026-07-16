<x-admin.layouts.layout title="Contact messages">
    <div class="mx-auto max-w-5xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Contact messages</h1>
        </div>

        @if ($messages->isEmpty())
            <p class="text-sm text-ink/50">No messages yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Message</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($messages as $message)
                            <tr>
                                <td class="px-4 py-3 text-ink">{{ $message->name }}</td>
                                <td class="px-4 py-3 text-ink/70">{{ $message->email }}</td>
                                <td class="px-4 py-3 text-ink/70">{{ $message->phone ?? '—' }}</td>
                                <td class="max-w-sm truncate px-4 py-3 text-ink/60" title="{{ $message->message }}">
                                    {{ $message->message }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-ink/50">{{ $message->createdAt->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $message->id) }}" onsubmit="return confirm('Delete this message?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-madder transition-colors hover:underline">Delete</button>
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
