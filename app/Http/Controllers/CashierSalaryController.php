<?php

namespace App\Http\Controllers;

use App\Actions\Payroll\CreateCashierSalaryAction;
use App\Http\Requests\StoreCashierSalaryRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;

class CashierSalaryController extends Controller
{
    public function store(
        StoreCashierSalaryRequest $request,
        Account $account,
        CreateCashierSalaryAction $action,
    ): RedirectResponse {
        $action->run($account, $request->validated());

        return redirect()->route('accounts.cashiers.index', $account);
    }
}
