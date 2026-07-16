<?php

namespace App\Dto\Order;

use App\Models\OrderItem;

final readonly class OrderItemDto
{
    public function __construct(
        public int $id,
        public ?int $productId,
        public string $name,
        public ?string $variantLabel,
        public ?string $image,
        public float $unitPrice,
        public int $quantity,
        public float $lineTotal,
    ) {}

    public static function fromModel(OrderItem $item): self
    {
        return new self(
            id: $item->id,
            productId: $item->product_id,
            name: $item->name,
            variantLabel: $item->variant_label,
            image: $item->image,
            unitPrice: (float) $item->unit_price,
            quantity: $item->quantity,
            lineTotal: (float) $item->line_total,
        );
    }
}
