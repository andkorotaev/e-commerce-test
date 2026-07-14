<?php

namespace App\Dto\Cart;

use Illuminate\Support\Collection;

final readonly class CartSummaryDto
{
    /**
     * @param  Collection<int, CartLineDto>  $lines
     */
    public function __construct(
        public Collection $lines,
        public float $subtotal,
        public float $discount,
        public float $delivery,
        public float $total,
    ) {}

    public function itemCount(): int
    {
        return $this->lines->sum('quantity');
    }

    public static function fromLines(Collection $lines, float $freeShippingThreshold, float $flatShippingFee): self
    {
        $subtotal = round($lines->sum(fn (CartLineDto $line) => $line->lineTotal()), 2);
        $discount = round($lines->sum(fn (CartLineDto $line) => $line->lineSavings()), 2);
        $delivery = $lines->isEmpty() || $subtotal >= $freeShippingThreshold ? 0.0 : $flatShippingFee;
        $total = round($subtotal + $delivery, 2);

        return new self(
            lines: $lines,
            subtotal: $subtotal,
            discount: $discount,
            delivery: $delivery,
            total: $total,
        );
    }
}
