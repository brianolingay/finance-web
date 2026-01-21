<?php

namespace App\Actions\Inventory;

use App\DTOs\InventoryMovementData;
use App\Models\InventoryMovement;

class RecordInventoryMovementAction
{
    public function run(InventoryMovementData $data): InventoryMovement
    {
        return InventoryMovement::query()->firstOrCreate(
            [
                'account_id' => $data->accountId,
                'product_id' => $data->productId,
                'movement_type' => $data->movementType,
                'source_type' => $data->source->getMorphClass(),
                'source_id' => $data->source->getKey(),
            ],
            [
                'quantity_delta' => $data->quantityDelta,
                'unit_cost_cents' => $data->unitCostCents,
                'occurred_at' => $data->occurredAt,
            ],
        );
    }
}
