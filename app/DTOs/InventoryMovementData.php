<?php

namespace App\DTOs;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class InventoryMovementData
{
    public function __construct(
        public readonly int $accountId,
        public readonly int $productId,
        public readonly string $movementType,
        public readonly int $quantityDelta,
        public readonly ?int $unitCostCents,
        public readonly Model $source,
        public readonly CarbonInterface $occurredAt,
    ) {}
}
