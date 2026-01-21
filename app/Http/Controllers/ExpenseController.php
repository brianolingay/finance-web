<?php

namespace App\Http\Controllers;

use App\Actions\Finance\CreateExpenseAction;
use App\Actions\Finance\ExpenseIndexQuery;
use App\DTOs\ExpenseData;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Account $account, ExpenseIndexQuery $query): Response
    {
        $this->authorize('manage-finance', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/expenses/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'categories' => $payload['categories'],
            'expenses' => $payload['expenses']->through(
                fn ($expense) => ExpenseResource::make($expense)->resolve(),
            ),
            'totals' => [
                'total_cents' => $payload['totals'],
            ],
        ]);
    }

    public function store(
        StoreExpenseRequest $request,
        Account $account,
        CreateExpenseAction $action,
    ): RedirectResponse {
        $this->authorize('manage-finance', $account);

        $action->run($account, ExpenseData::fromRequest($request));

        return redirect()->route('accounts.expenses.index', $account);
    }
}
