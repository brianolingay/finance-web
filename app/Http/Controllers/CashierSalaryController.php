<?php

namespace App\Http\Controllers;

use App\Actions\Payroll\CreateCashierSalaryAction;
use App\DTOs\CashierSalaryData;
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
        $this->authorize('manage-cashiers', $account);

        $action->run($account, CashierSalaryData::fromRequest($request));

        return redirect()->route('accounts.cashiers.index', $account);
    }
}
