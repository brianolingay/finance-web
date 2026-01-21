<?php

namespace App\Actions\Inventory;

use App\DTOs\InventoryPurchaseData;
use App\Events\InventoryPurchaseRecorded;
use App\Models\Account;
use App\Models\InventoryPurchase;
use Illuminate\Support\Facades\DB;

class RecordInventoryPurchasePaymentAction
{
    public function run(Account $account, InventoryPurchaseData $data): InventoryPurchase
    {
        $paidAt = $data->paidAt ?? now();

        $purchase = DB::transaction(function () use ($account, $data, $paidAt): InventoryPurchase {
            $purchase = InventoryPurchase::query()->create([
                'account_id' => $account->id,
                'supplier_id' => $data->supplierId,
                'goods_receipt_id' => $data->goodsReceiptId,
                'status' => $data->status ?? 'paid',
                'total_cents' => $data->totalCents,
                'currency' => $data->currency,
                'paid_at' => $paidAt,
            ]);

            event(new InventoryPurchaseRecorded($purchase));

            return $purchase;
        });

        return $purchase;
    }
}
