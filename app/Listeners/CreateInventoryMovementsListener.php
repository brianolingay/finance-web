<?php

namespace App\Listeners;

use App\Actions\Inventory\RecordInventoryMovementAction;
use App\DTOs\InventoryMovementData;
use App\Events\GoodsReceiptCreated;
use App\Events\SaleCompleted;

class CreateInventoryMovementsListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private RecordInventoryMovementAction $recordInventoryMovementAction,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof SaleCompleted) {
            $sale = $event->sale->loadMissing('saleItems');

            foreach ($sale->saleItems as $item) {
                $this->recordInventoryMovementAction->run(
                    new InventoryMovementData(
                        $sale->account_id,
                        $item->product_id,
                        'sale',
                        -1 * $item->quantity,
                        null,
                        $sale,
                        $sale->occurred_at,
                    ),
                );
            }

            return;
        }

        if ($event instanceof GoodsReceiptCreated) {
            $receipt = $event->goodsReceipt->loadMissing('goodsReceiptItems');

            foreach ($receipt->goodsReceiptItems as $item) {
                $this->recordInventoryMovementAction->run(
                    new InventoryMovementData(
                        $receipt->account_id,
                        $item->product_id,
                        'purchase_receipt',
                        $item->quantity,
                        $item->unit_cost_cents,
                        $receipt,
                        $receipt->received_at,
                    ),
                );
            }
        }
    }
}
