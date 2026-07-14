<?php

namespace App\Dto\Cart;

final readonly class CartLineDto
{
    public function __construct(
        public int $productId,
        public ?int $variantId,
        public string $name,
        public ?string $slug,
        public ?string $image,
        public ?string $variantLabel,
        public float $unitPrice,
        public ?float $unitOldPrice,
        public int $quantity,
        public int $availableStock,
    ) {}

    public function lineTotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    public function lineSavings(): float
    {
        if ($this->unitOldPrice === null || $this->unitOldPrice <= $this->unitPrice) {
            return 0.0;
        }

        return ($this->unitOldPrice - $this->unitPrice) * $this->quantity;
    }
}
