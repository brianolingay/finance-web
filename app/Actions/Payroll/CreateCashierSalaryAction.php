<?php

namespace App\Actions\Payroll;

use App\DTOs\CashierSalaryData;
use App\Events\CashierSalaryCreated;
use App\Models\Account;
use App\Models\CashierSalary;
use Illuminate\Support\Facades\DB;

class CreateCashierSalaryAction
{
    public function run(Account $account, CashierSalaryData $data): CashierSalary
    {
        $paidAt = $data->paidAt ?? now();

        $salary = DB::transaction(function () use ($account, $data, $paidAt): CashierSalary {
            $salary = CashierSalary::query()->create([
                'account_id' => $account->id,
                'cashier_id' => $data->cashierId,
                'salary_rule_id' => $data->salaryRuleId,
                'amount_cents' => $data->amountCents,
                'currency' => $data->currency,
                'paid_at' => $paidAt,
                'status' => $data->status ?? 'paid',
            ]);

            event(new CashierSalaryCreated($salary));

            return $salary;
        });

        return $salary;
    }
}
