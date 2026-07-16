<x-front.layouts.layout title="Історія замовлень" description="Історія ваших замовлень в OCRE.">
    <x-front.account.layout title="Історія замовлень">
        @if ($orders->isEmpty())
            <p class="font-mono text-xs uppercase tracking-widest text-ink/40">
                У вас поки немає замовлень
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone/40 font-mono text-xs uppercase tracking-widest text-ink/40">
                            <th class="py-2 pr-4">Номер</th>
                            <th class="py-2 pr-4">Дата</th>
                            <th class="py-2 pr-4">Сума</th>
                            <th class="py-2">Статус</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone/20">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="py-3 pr-4 font-mono text-ink">{{ $order->orderNumber() }}</td>
                                <td class="py-3 pr-4 text-ink/70">{{ $order->createdAt->format('d.m.Y') }}</td>
                                <td class="py-3 pr-4 font-mono text-ink">{{ number_format($order->total, 0, ',', ' ') }} ₴</td>
                                <td class="py-3 text-ink/70">{{ config("shop.order_statuses.{$order->status}", $order->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-front.account.layout>
</x-front.layouts.layout>
