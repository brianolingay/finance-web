<?php

namespace App\Actions\Finance;

use App\Models\Account;
use App\Models\Income;
use Illuminate\Pagination\LengthAwarePaginator;

class IncomeIndexQuery
{
    /**
     * @return array{incomes: LengthAwarePaginator, categories: \Illuminate\Support\Collection<int, \App\Models\Category>, totals: array<string, int>}
     */
    public function run(Account $account): array
    {
        $incomes = Income::query()
            ->forAccount($account->id)
            ->occurredAtDesc()
            ->paginate(10);

        $categories = $account->categories()
            ->orderBy('name')
            ->get(['id', 'name']);

        $totals = Income::query()
            ->forAccount($account->id)
            ->selectRaw('currency, SUM(amount_cents) as total_cents')
            ->groupBy('currency')
            ->pluck('total_cents', 'currency')
            ->map(fn ($total): int => (int) $total)
            ->all();

        return [
            'incomes' => $incomes,
            'categories' => $categories,
            'totals' => $totals,
        ];
    }
}
