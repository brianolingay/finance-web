<?php

namespace App\DTOs;

class SaleItemData
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly int $unitPriceCents,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['product_id'],
            (int) $data['quantity'],
            (int) $data['unit_price_cents'],
        );
    }

    public function lineTotalCents(): int
    {
        return $this->quantity * $this->unitPriceCents;
    }
}
