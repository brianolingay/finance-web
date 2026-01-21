<?php

namespace App\Actions\Inventory;

use App\Events\GoodsReceiptCreated;
use App\Models\Account;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateGoodsReceiptAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): GoodsReceipt
    {
        if (! array_key_exists('items', $data) || ! is_array($data['items']) || $data['items'] === []) {
            throw ValidationException::withMessages([
                'items' => 'At least one item is required.',
            ]);
        }

        $receivedAt = isset($data['received_at'])
            ? Carbon::parse($data['received_at'])
            : now();

        $receipt = DB::transaction(function () use ($account, $data, $receivedAt): GoodsReceipt {
            $items = collect($data['items'])->map(function (array $item) {
                $lineTotal = (int) $item['quantity'] * (int) $item['unit_cost_cents'];

                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_cost_cents' => (int) $item['unit_cost_cents'],
                    'line_total_cents' => $lineTotal,
                ];
            });

            $receipt = GoodsReceipt::query()->create([
                'account_id' => $account->id,
                'supplier_id' => $data['supplier_id'] ?? null,
                'status' => $data['status'] ?? 'received',
                'reference' => $data['reference'] ?? null,
                'received_at' => $receivedAt,
                'notes' => $data['notes'] ?? null,
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
