<?php

namespace App\Dto\Order;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final readonly class OrderDto
{
    /**
     * @param  Collection<int, OrderItemDto>  $items
     */
    public function __construct(
        public int $id,
        public ?int $userId,
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $email,
        public string $city,
        public string $address,
        public ?string $comment,
        public string $deliveryCarrier,
        public string $deliveryType,
        public ?string $deliveryPoint,
        public string $paymentMethod,
        public float $subtotal,
        public float $discount,
        public float $deliveryFee,
        public float $total,
        public string $status,
        public Carbon $createdAt,
        public Collection $items,
    ) {}

    public static function fromModel(Order $order): self
    {
        return new self(
            id: $order->id,
            userId: $order->user_id,
            firstName: $order->first_name,
            lastName: $order->last_name,
            phone: $order->phone,
            email: $order->email,
            city: $order->city,
            address: $order->address,
            comment: $order->comment,
            deliveryCarrier: $order->delivery_carrier,
            deliveryType: $order->delivery_type,
            deliveryPoint: $order->delivery_point,
            paymentMethod: $order->payment_method,
            subtotal: (float) $order->subtotal,
            discount: (float) $order->discount,
            deliveryFee: (float) $order->delivery_fee,
            total: (float) $order->total,
            status: $order->status,
            createdAt: $order->created_at,
            items: $order->items->map(fn (OrderItem $item) => OrderItemDto::fromModel($item)),
        );
    }

    /**
     * A human-friendly order number — no separate stored column, just a
     * formatted view of the row's own id.
     */
    public function orderNumber(): string
    {
        return 'OCRE-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function deliveryCarrierLabel(): string
    {
        return config("shop.delivery_carriers.{$this->deliveryCarrier}", $this->deliveryCarrier);
    }

    public function deliveryTypeLabel(): string
    {
        return config("shop.delivery_types.{$this->deliveryType}", $this->deliveryType);
    }

    public function paymentMethodLabel(): string
    {
        return config("shop.payment_methods.{$this->paymentMethod}", $this->paymentMethod);
    }
}
