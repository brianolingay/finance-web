<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\AccountDashboardQuery;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use Inertia\Inertia;
use Inertia\Response;

class AccountDashboardController extends Controller
{
    public function index(Account $account, AccountDashboardQuery $query): Response
    {
        $this->authorize('view-account', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/dashboard', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
            ],
            'totals' => [
                'credit_cents' => $payload['totals']['credit'],
                'debit_cents' => $payload['totals']['debit'],
                'net_cents' => $payload['totals']['net'],
            ],
            'transactions' => $payload['transactions']->through(
                fn ($transaction) => TransactionResource::make($transaction)->resolve(),
            ),
        ]);
    }
}
