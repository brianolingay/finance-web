<?php

namespace App\Actions\Payroll;

use App\Models\Account;
use App\Models\Cashier;
use Illuminate\Pagination\LengthAwarePaginator;

class CashiersIndexQuery
{
    /**
     * @return array{cashiers: LengthAwarePaginator}
     */
    public function run(Account $account): array
    {
        $cashiers = Cashier::query()
            ->where('account_id', $account->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return [
            'cashiers' => $cashiers,
        ];
    }
}
