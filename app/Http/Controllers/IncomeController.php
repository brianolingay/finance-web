<?php

namespace App\Http\Controllers;

use App\Actions\Finance\CreateIncomeAction;
use App\Actions\Finance\IncomeIndexQuery;
use App\DTOs\IncomeData;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Account $account, IncomeIndexQuery $query): Response
    {
        $this->authorize('manage-finance', $account);

        $payload = $query->run($account);

        return Inertia::render('accounts/incomes/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'categories' => $payload['categories'],
            'incomes' => $payload['incomes']->through(
                fn ($income) => IncomeResource::make($income)->resolve(),
            ),
            'totals' => [
                'total_cents' => $payload['totals'],
            ],
        ]);
    }

    public function store(
        StoreIncomeRequest $request,
        Account $account,
        CreateIncomeAction $action,
    ): RedirectResponse {
        $this->authorize('manage-finance', $account);

        $action->run($account, IncomeData::fromRequest($request));

        return redirect()->route('accounts.incomes.index', $account);
    }
}
