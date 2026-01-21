<?php

namespace App\Http\Controllers;

use App\Actions\POS\CompleteSaleAction;
use App\Actions\POS\NewSaleQuery;
use App\DTOs\SaleData;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\ProductResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function create(Request $request, Account $account, NewSaleQuery $query): Response
    {
        $this->authorize('create-sale', $account);

        $payload = $query->run($account, $request->user());

        return Inertia::render('accounts/pos/new-sale', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'cashierId' => $payload['cashierId'],
            'products' => ProductResource::collection($payload['products'])->resolve(),
        ]);
    }

    public function store(
        StoreSaleRequest $request,
        Account $account,
        CompleteSaleAction $action,
    ): RedirectResponse {
        $action->run($account, SaleData::fromRequest($request), $request->user());

        return redirect()->route('accounts.pos.sales.create', $account);
    }
}
