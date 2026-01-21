<?php

namespace App\Actions\Inventory;

use App\Models\Account;
use App\Models\GoodsReceipt;
use Illuminate\Pagination\LengthAwarePaginator;

class GoodsReceiptIndexQuery
{
    /**
     * @return array{products: \Illuminate\Support\Collection<int, \App\Models\Product>, suppliers: \Illuminate\Support\Collection<int, \App\Models\Supplier>, receipts: LengthAwarePaginator}
     */
    public function run(Account $account): array
    {
        $products = $account->products()
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit_cost_cents']);

        $suppliers = $account->suppliers()
            ->orderBy('name')
            ->get(['id', 'name']);

        $receipts = GoodsReceipt::query()
            ->where('account_id', $account->id)
            ->latest('received_at')
            ->paginate(10);

        return [
            'products' => $products,
            'suppliers' => $suppliers,
            'receipts' => $receipts,
        ];
    }
}
