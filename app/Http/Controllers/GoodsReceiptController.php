<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\CreateGoodsReceiptAction;
use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GoodsReceiptController extends Controller
{
    public function create(Account $account): Response
    {
        $products = $account->products()
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit_cost_cents']);

        $suppliers = $account->suppliers()
            ->orderBy('name')
            ->get(['id', 'name']);

        $receipts = $account->goodsReceipts()
            ->latest('received_at')
            ->paginate(10);

        return Inertia::render('accounts/inventory/receipts', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'products' => $products,
            'suppliers' => $suppliers,
            'receipts' => $receipts,
        ]);
    }

    public function store(
        StoreGoodsReceiptRequest $request,
        Account $account,
        CreateGoodsReceiptAction $action,
    ): RedirectResponse {
        $action->run($account, $request->validated());

        return redirect()->route('accounts.inventory.receipts.create', $account);
    }
}
