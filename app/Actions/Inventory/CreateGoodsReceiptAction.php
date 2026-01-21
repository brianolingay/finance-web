<?php

namespace App\Actions\Inventory;

use App\DTOs\GoodsReceiptData;
use App\Events\GoodsReceiptCreated;
use App\Models\Account;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateGoodsReceiptAction
{
    public function run(Account $account, GoodsReceiptData $data): GoodsReceipt
    {
        if ($data->items === []) {
            throw ValidationException::withMessages([
                'items' => 'At least one item is required.',
            ]);
        }

        $receivedAt = $data->receivedAt ?? now();

        $receipt = DB::transaction(function () use ($account, $data, $receivedAt): GoodsReceipt {
            $items = collect($data->items)->map(fn ($item) => [
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
                'unit_cost_cents' => $item->unitCostCents,
                'line_total_cents' => $item->lineTotalCents(),
            ]);

            $receipt = GoodsReceipt::query()->create([
                'account_id' => $account->id,
                'supplier_id' => $data->supplierId,
                'status' => $data->status ?? 'received',
                'reference' => $data->reference,
                'received_at' => $receivedAt,
                'notes' => $data->notes,
            ]);

            $items->each(function (array $item) use ($receipt): void {
                GoodsReceiptItem::query()->create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost_cents' => $item['unit_cost_cents'],
                    'line_total_cents' => $item['line_total_cents'],
                ]);
            });

            return $receipt->load('goodsReceiptItems.product');
        });

        event(new GoodsReceiptCreated($receipt));

        return $receipt;
    }
}
