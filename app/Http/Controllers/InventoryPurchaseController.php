<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\RecordInventoryPurchasePaymentAction;
use App\DTOs\InventoryPurchaseData;
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
        $this->authorize('manage-inventory', $account);

        $action->run($account, InventoryPurchaseData::fromRequest($request));

        return redirect()->route('accounts.inventory.receipts.create', $account);
    }
}
