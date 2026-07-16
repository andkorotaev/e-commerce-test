<x-admin.layouts.layout :title="$user->name">
    <div class="mx-auto max-w-4xl px-8 py-10">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">{{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-ink/60 transition-colors hover:text-ink hover:underline">
                ← All users
            </a>
        </div>

        <dl class="mb-10 grid grid-cols-2 gap-6 rounded-lg border border-ink/10 bg-white p-6 sm:grid-cols-3">
            <div>
                <dt class="text-xs uppercase tracking-wide text-ink/50">Email</dt>
                <dd class="mt-1 text-sm text-ink">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-ink/50">Registered</dt>
                <dd class="mt-1 text-sm text-ink">{{ $user->createdAt->format('d.m.Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-ink/50">Orders</dt>
                <dd class="mt-1 text-sm text-ink">{{ $user->ordersCount }}</dd>
            </div>
        </dl>

        <h2 class="mb-4 text-sm font-medium text-ink">Order history</h2>

        @if ($orders->isEmpty())
            <p class="text-sm text-ink/50">No orders yet.</p>
        @else
            <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Number</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-ink">{{ $order->orderNumber() }}</td>
                                <td class="px-4 py-3 text-ink/60">{{ $order->createdAt->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-3 font-mono text-ink">{{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-3 text-xs text-ink/60">
                                    {{ config("shop.order_statuses.{$order->status}", $order->status) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-ink/60 transition-colors hover:text-ink hover:underline">
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
