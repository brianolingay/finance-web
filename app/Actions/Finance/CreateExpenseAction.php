<?php

namespace App\Actions\Finance;

use App\DTOs\ExpenseData;
use App\Events\ExpenseCreated;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class CreateExpenseAction
{
    public function run(Account $account, ExpenseData $data): Expense
    {
        $expense = DB::transaction(function () use ($account, $data): Expense {
            $expense = Expense::query()->create([
                'account_id' => $account->id,
                'category_id' => $data->categoryId,
                'description' => $data->description,
                'status' => $data->status ?? 'posted',
                'amount_cents' => $data->amountCents,
                'currency' => $data->currency,
                'occurred_at' => $data->occurredAt ?? now(),
                'paid_at' => $data->paidAt,
            ]);

            return $expense;
        });

        event(new ExpenseCreated($expense));

        return $expense;
    }
}
