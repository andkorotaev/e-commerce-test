<x-admin.layouts.layout title="Users">
    <div class="mx-auto max-w-4xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Users</h1>
        </div>

        @if ($users->isEmpty())
            <p class="text-sm text-ink/50">No registered users yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Registered</th>
                            <th class="px-4 py-3 font-medium">Orders</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-ink">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-ink/70">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-ink/60">{{ $user->createdAt->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 font-mono text-ink">{{ $user->ordersCount }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-ink/60 transition-colors hover:text-ink hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin.layouts.layout>
