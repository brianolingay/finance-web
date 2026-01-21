<?php

namespace App\Actions\Payroll;

use App\Events\CashierSalaryCreated;
use App\Models\Account;
use App\Models\CashierSalary;
use Illuminate\Support\Carbon;

class CreateCashierSalaryAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function run(Account $account, array $data): CashierSalary
    {
        $paidAt = isset($data['paid_at'])
            ? Carbon::parse($data['paid_at'])
            : now();

        $salary = CashierSalary::query()->create([
            'account_id' => $account->id,
            'cashier_id' => $data['cashier_id'],
            'salary_rule_id' => $data['salary_rule_id'] ?? null,
            'amount_cents' => $data['amount_cents'],
            'currency' => $data['currency'],
            'paid_at' => $paidAt,
            'status' => $data['status'] ?? 'paid',
        ]);

        event(new CashierSalaryCreated($salary));

        return $salary;
    }
}
