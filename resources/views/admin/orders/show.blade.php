<x-admin.layouts.layout title="Order {{ $order->orderNumber() }}">
    <div class="mx-auto max-w-3xl px-8 py-10">
        <a href="{{ route('admin.orders.index') }}" class="mb-4 inline-block text-sm text-ink/50 transition-colors hover:text-ink">
            ← Orders
        </a>

        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-lg font-medium text-ink">Order {{ $order->orderNumber() }}</h1>
            <span class="font-mono text-xs text-ink/40">{{ $order->createdAt->format('d.m.Y H:i') }}</span>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="rounded-lg border border-ink/10 bg-white p-5">
                <h2 class="mb-3 text-xs font-medium uppercase tracking-wide text-ink/50">Customer</h2>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Name</dt><dd class="text-ink">{{ $order->firstName }} {{ $order->lastName }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Phone</dt><dd class="text-ink">{{ $order->phone }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Email</dt><dd class="text-ink">{{ $order->email }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">City</dt><dd class="text-ink">{{ $order->city }}</dd></div>
                    @if ($order->address)
                        <div class="flex justify-between gap-4"><dt class="text-ink/50">Address</dt><dd class="text-ink text-right">{{ $order->address }}</dd></div>
                    @endif
                    @if ($order->comment)
                        <div class="flex justify-between gap-4"><dt class="text-ink/50">Comment</dt><dd class="text-ink text-right">{{ $order->comment }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Account</dt><dd class="text-ink">{{ $order->userId ? 'Registered user #'.$order->userId : 'Guest' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-ink/10 bg-white p-5">
                <h2 class="mb-3 text-xs font-medium uppercase tracking-wide text-ink/50">Delivery &amp; payment</h2>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Carrier</dt><dd class="text-ink">{{ $order->deliveryCarrierLabel() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Type</dt><dd class="text-ink">{{ $order->deliveryTypeLabel() }}</dd></div>
                    @if ($order->deliveryPoint)
                        <div class="flex justify-between gap-4"><dt class="text-ink/50">Point</dt><dd class="text-ink">{{ $order->deliveryPoint }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-4"><dt class="text-ink/50">Payment</dt><dd class="text-ink">{{ $order->paymentMethodLabel() }}</dd></div>
                </dl>

                <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="mt-5 border-t border-ink/10 pt-4">
                    @csrf
                    @method('PUT')
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-ink/50">Status</label>
                    <div class="flex gap-2">
                        <select name="status" class="w-full rounded-md border border-ink/15 bg-white px-3 py-2 text-sm text-ink focus:border-ink focus:outline-none">
                            @foreach (config('shop.order_statuses') as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="shrink-0 rounded-md bg-ink px-4 py-2 text-sm font-medium text-bone transition-colors hover:bg-ink/85">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-ink/10 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-ink/10 bg-stone/10 text-xs uppercase tracking-wide text-ink/50">
                    <tr>
                        <th class="px-4 py-3 font-medium">Item</th>
                        <th class="px-4 py-3 font-medium">Unit price</th>
                        <th class="px-4 py-3 font-medium">Qty</th>
                        <th class="px-4 py-3 font-medium">Line total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/10">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-ink">
                                {{ $item->name }}
                                @if ($item->variantLabel)
                                    <span class="block font-mono text-xs text-ink/40">{{ $item->variantLabel }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-ink/70">{{ number_format($item->unitPrice, 2) }}</td>
                            <td class="px-4 py-3 text-ink/70">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 font-mono text-ink">{{ number_format($item->lineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-ink/10 text-sm">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-ink/50">Subtotal</td>
                        <td class="px-4 py-2 font-mono text-ink">{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if ($order->discount > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-ink/50">Discount</td>
                            <td class="px-4 py-2 font-mono text-madder">−{{ number_format($order->discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-ink/50">Delivery</td>
                        <td class="px-4 py-2 font-mono text-ink">{{ number_format($order->deliveryFee, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-medium text-ink">Total</td>
                        <td class="px-4 py-3 font-mono text-base font-medium text-ink">{{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-admin.layouts.layout>
