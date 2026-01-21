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

        $creditTotals = $account->transactions()
            ->where('direction', 'credit')
            ->selectRaw('currency, SUM(amount_cents) as total_cents')
            ->groupBy('currency')
            ->pluck('total_cents', 'currency')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $debitTotals = $account->transactions()
            ->where('direction', 'debit')
            ->selectRaw('currency, SUM(amount_cents) as total_cents')
            ->groupBy('currency')
            ->pluck('total_cents', 'currency')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $netTotals = collect(array_keys($creditTotals))
            ->merge(array_keys($debitTotals))
            ->unique()
            ->mapWithKeys(function (string $currency) use ($creditTotals, $debitTotals): array {
                $creditTotal = $creditTotals[$currency] ?? 0;
                $debitTotal = $debitTotals[$currency] ?? 0;

                return [$currency => $creditTotal - $debitTotal];
            })
            ->all();

        return Inertia::render('accounts/dashboard', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
            ],
            'totals' => [
                'credit_cents' => $creditTotals,
                'debit_cents' => $debitTotals,
                'net_cents' => $netTotals,
            ],
            'transactions' => $transactions,
        ]);
    }
}
