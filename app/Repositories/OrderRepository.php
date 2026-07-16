<?php

namespace App\Repositories;

use App\Dto\Order\OrderDto;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderRepository
{
    protected const array WITH = ['items'];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): OrderDto
    {
        $order = Order::create($attributes);

        return OrderDto::fromModel($order->load(self::WITH));
    }

    public function find(int $id): ?OrderDto
    {
        $order = Order::with(self::WITH)->find($id);

        return $order ? OrderDto::fromModel($order) : null;
    }

    /**
     * Newest first — the account cabinet's order history.
     *
     * @return Collection<int, OrderDto>
     */
    public function forUser(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(self::WITH)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Order $order) => OrderDto::fromModel($order));
    }
}
