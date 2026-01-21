<?php

namespace App\Http\Controllers;

use App\Actions\Payroll\CashiersIndexQuery;
use App\Actions\Payroll\InviteCashierAction;
use App\DTOs\CashierInviteData;
use App\Http\Requests\StoreCashierRequest;
use App\Http\Resources\CashierResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CashierController extends Controller
{
    public function index(Account $account, CashiersIndexQuery $query): Response
    {
        $this->authorize('manage-cashiers', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/cashiers/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'cashiers' => $payload['cashiers']->through(
                fn ($cashier) => CashierResource::make($cashier)->resolve(),
            ),
        ]);
    }

    public function store(
        StoreCashierRequest $request,
        Account $account,
        InviteCashierAction $action,
    ): RedirectResponse {
        $this->authorize('manage-cashiers', $account);

        $action->run($account, CashierInviteData::fromRequest($request));

        return redirect()->route('accounts.cashiers.index', $account);
    }
}
