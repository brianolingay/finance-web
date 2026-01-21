<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\RecordInventoryPurchasePaymentAction;
use App\Http\Requests\StoreInventoryPurchaseRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;

class InventoryPurchaseController extends Controller
{
    public function store(
        StoreInventoryPurchaseRequest $request,
        Account $account,
        RecordInventoryPurchasePaymentAction $action,
    ): RedirectResponse {
        $action->run($account, $request->validated());

        return redirect()->route('accounts.inventory.receipts.create', $account);
    }
}
