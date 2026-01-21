<?php

namespace App\Http\Controllers;

use App\Actions\Finance\CreateExpenseAction;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Account $account): Response
    {
        $expenses = $account->expenses()
            ->latest('occurred_at')
            ->paginate(10);

        $categories = $account->categories()
            ->orderBy('name')
            ->get(['id', 'name']);

        $totalCents = (int) $account->expenses()
            ->sum('amount_cents');

        return Inertia::render('accounts/expenses/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'categories' => $categories,
            'expenses' => $expenses,
            'totals' => [
                'total_cents' => $totalCents,
            ],
        ]);
    }

    public function store(
        StoreExpenseRequest $request,
        Account $account,
        CreateExpenseAction $action,
    ): RedirectResponse {
        $action->run($account, $request->validated());

        return redirect()->route('accounts.expenses.index', $account);
    }
}
