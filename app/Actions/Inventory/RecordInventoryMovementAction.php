<?php

namespace App\Actions\Inventory;

use App\Models\InventoryMovement;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class RecordInventoryMovementAction
{
    public function run(
        int $accountId,
        int $productId,
        string $movementType,
        int $quantityDelta,
        ?int $unitCostCents,
        Model $source,
        CarbonInterface $occurredAt,
    ): InventoryMovement {
        return InventoryMovement::query()->firstOrCreate(
            [
                'account_id' => $accountId,
                'product_id' => $productId,
                'movement_type' => $movementType,
                'source_type' => $source->getMorphClass(),
                'source_id' => $source->getKey(),
            ],
            [
                'quantity_delta' => $quantityDelta,
                'unit_cost_cents' => $unitCostCents,
                'occurred_at' => $occurredAt,
            ],
        );
    }
}
