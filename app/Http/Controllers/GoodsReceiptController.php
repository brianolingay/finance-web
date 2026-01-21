<?php

namespace App\Http\Controllers;

use App\Actions\Inventory\CreateGoodsReceiptAction;
use App\Actions\Inventory\GoodsReceiptIndexQuery;
use App\DTOs\GoodsReceiptData;
use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Http\Resources\GoodsReceiptResource;
use App\Http\Resources\ProductResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GoodsReceiptController extends Controller
{
    public function create(Account $account, GoodsReceiptIndexQuery $query): Response
    {
        $this->authorize('manage-inventory', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/inventory/receipts', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'products' => ProductResource::collection($payload['products'])->resolve(),
            'suppliers' => $payload['suppliers'],
            'receipts' => $payload['receipts']->through(
                fn ($receipt) => GoodsReceiptResource::make($receipt)->resolve(),
            ),
        ]);
    }

    public function store(
        StoreGoodsReceiptRequest $request,
        Account $account,
        CreateGoodsReceiptAction $action,
    ): RedirectResponse {
        $this->authorize('manage-inventory', $account);

        $action->run($account, GoodsReceiptData::fromRequest($request));

        return redirect()->route('accounts.inventory.receipts.create', $account);
    }
}
