<?php

namespace App\Actions\Dashboard;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class AccountDashboardQuery
{
    /**
     * @return array{transactions: LengthAwarePaginator, totals: array{credit: array<string, int>, debit: array<string, int>, net: array<string, int>}}
     */
    public function run(Account $account): array
    {
        $transactions = Transaction::query()
            ->forAccount($account->id)
            ->occurredAtDesc()
            ->paginate(10);

        $creditTotals = Transaction::query()
            ->forAccount($account->id)
            ->where('direction', 'credit')
            ->selectRaw('currency, SUM(amount_cents) as total_cents')
            ->groupBy('currency')
            ->pluck('total_cents', 'currency')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $debitTotals = Transaction::query()
            ->forAccount($account->id)
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

        return [
            'transactions' => $transactions,
            'totals' => [
                'credit' => $creditTotals,
                'debit' => $debitTotals,
                'net' => $netTotals,
            ],
        ];
    }
}
