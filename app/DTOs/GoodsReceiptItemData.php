<?php

namespace App\DTOs;

class GoodsReceiptItemData
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly int $unitCostCents,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['product_id'],
            (int) $data['quantity'],
            (int) $data['unit_cost_cents'],
        );
    }

    public function lineTotalCents(): int
    {
        return $this->quantity * $this->unitCostCents;
    }
}
