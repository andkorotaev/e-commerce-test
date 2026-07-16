<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(int $orderId, array $attributes): void
    {
        OrderItem::create([...$attributes, 'order_id' => $orderId]);
    }
}
