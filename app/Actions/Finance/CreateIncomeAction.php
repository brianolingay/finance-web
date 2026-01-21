<?php

namespace App\Actions\Finance;

use App\DTOs\IncomeData;
use App\Events\IncomeCreated;
use App\Models\Account;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class CreateIncomeAction
{
    public function run(Account $account, IncomeData $data): Income
    {
        $income = DB::transaction(function () use ($account, $data): Income {
            $income = Income::query()->create([
                'account_id' => $account->id,
                'category_id' => $data->categoryId,
                'description' => $data->description,
                'status' => $data->status ?? 'posted',
                'amount_cents' => $data->amountCents,
                'currency' => $data->currency,
                'occurred_at' => $data->occurredAt ?? now(),
                'paid_at' => $data->paidAt,
            ]);

            event(new IncomeCreated($income));

            return $income;
        });

        return $income;
    }
}
