<?php

namespace App\Actions\Finance;

use App\Events\ExpenseCreated;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Support\Carbon;

class CreateExpenseAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): Expense
    {
        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : now();

        $expense = Expense::query()->create([
            'account_id' => $account->id,
            'category_id' => $data['category_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'posted',
            'amount_cents' => $data['amount_cents'],
            'currency' => $data['currency'],
            'occurred_at' => $occurredAt,
            'paid_at' => isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : null,
        ]);

        event(new ExpenseCreated($expense));

        return $expense;
    }
}
