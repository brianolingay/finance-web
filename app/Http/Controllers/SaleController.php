<?php

namespace App\Http\Controllers;

use App\Actions\POS\CompleteSaleAction;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function create(Request $request, Account $account): Response
    {
        $products = $account->products()
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'unit_price_cents']);

        $cashierId = $account->cashiers()
            ->where('user_id', $request->user()->id)
            ->value('id');

        return Inertia::render('accounts/pos/new-sale', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'cashierId' => $cashierId,
            'products' => $products,
        ]);
    }

    public function store(
        StoreSaleRequest $request,
        Account $account,
        CompleteSaleAction $action,
    ): RedirectResponse {
        $data = $request->validated();

        if (! isset($data['cashier_id'])) {
            $cashierId = $account->cashiers()
                ->where('user_id', $request->user()->id)
                ->value('id');

            if ($cashierId) {
                $data['cashier_id'] = $cashierId;
            }
        }

        $action->run($account, $data);

        return redirect()->route('accounts.pos.sales.create', $account);
    }
}
