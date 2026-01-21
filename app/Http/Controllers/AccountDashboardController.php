<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Inertia\Inertia;
use Inertia\Response;

class AccountDashboardController extends Controller
{
    public function index(Account $account): Response
    {
        $transactions = $account->transactions()
            ->latest('occurred_at')
            ->paginate(10);

        $creditTotal = (int) $account->transactions()
            ->where('direction', 'credit')
            ->sum('amount_cents');

        $debitTotal = (int) $account->transactions()
            ->where('direction', 'debit')
            ->sum('amount_cents');

        return Inertia::render('accounts/dashboard', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
            ],
            'totals' => [
                'credit_cents' => $creditTotal,
                'debit_cents' => $debitTotal,
                'net_cents' => $creditTotal - $debitTotal,
            ],
            'transactions' => $transactions,
        ]);
    }
}
