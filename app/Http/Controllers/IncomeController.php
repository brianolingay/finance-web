<?php

namespace App\Http\Controllers;

use App\Actions\Finance\CreateIncomeAction;
use App\Http\Requests\StoreIncomeRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Account $account): Response
    {
        $incomes = $account->incomes()
            ->latest('occurred_at')
            ->paginate(10);

        $categories = $account->categories()
            ->orderBy('name')
            ->get(['id', 'name']);

        $totalCents = (int) $account->incomes()
            ->sum('amount_cents');

        return Inertia::render('accounts/incomes/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'categories' => $categories,
            'incomes' => $incomes,
            'totals' => [
                'total_cents' => $totalCents,
            ],
        ]);
    }

    public function store(
        StoreIncomeRequest $request,
        Account $account,
        CreateIncomeAction $action,
    ): RedirectResponse {
        $action->run($account, $request->validated());

        return redirect()->route('accounts.incomes.index', $account);
    }
}
