<?php

namespace App\Events;

use App\Models\InventoryPurchase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryPurchaseRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(public InventoryPurchase $inventoryPurchase) {}
}
