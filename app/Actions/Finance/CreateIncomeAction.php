<?php

namespace App\Actions\Finance;

use App\Events\IncomeCreated;
use App\Models\Account;
use App\Models\Income;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateIncomeAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): Income
    {
        $missing = [];

        if (! array_key_exists('amount_cents', $data)) {
            $missing['amount_cents'] = 'Amount is required.';
        }

        if (! array_key_exists('currency', $data)) {
            $missing['currency'] = 'Currency is required.';
        }

        if ($missing !== []) {
            throw ValidationException::withMessages($missing);
        }

        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : now();

        $income = DB::transaction(function () use ($account, $data, $occurredAt): Income {
            $income = Income::query()->create([
                'account_id' => $account->id,
                'category_id' => $data['category_id'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'posted',
                'amount_cents' => $data['amount_cents'],
                'currency' => $data['currency'],
                'occurred_at' => $occurredAt,
                'paid_at' => isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : null,
            ]);

            event(new IncomeCreated($income));

            return $income;
        });

        return $income;
    }
}
