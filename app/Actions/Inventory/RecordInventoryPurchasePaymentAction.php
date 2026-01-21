<?php

namespace App\Actions\Inventory;

use App\Events\InventoryPurchaseRecorded;
use App\Models\Account;
use App\Models\InventoryPurchase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RecordInventoryPurchasePaymentAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): InventoryPurchase
    {
        $paidAt = isset($data['paid_at'])
            ? Carbon::parse($data['paid_at'])
            : now();

        $purchase = DB::transaction(function () use ($account, $data, $paidAt): InventoryPurchase {
            $purchase = InventoryPurchase::query()->create([
                'account_id' => $account->id,
                'supplier_id' => $data['supplier_id'] ?? null,
                'goods_receipt_id' => $data['goods_receipt_id'] ?? null,
                'status' => $data['status'] ?? 'paid',
                'total_cents' => $data['total_cents'],
                'currency' => $data['currency'],
                'paid_at' => $paidAt,
            ]);

            event(new InventoryPurchaseRecorded($purchase));

            return $purchase;
        });

        return $purchase;
    }
}
