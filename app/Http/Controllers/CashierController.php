<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCashierRequest;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\Cashier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CashierController extends Controller
{
    public function index(Account $account): Response
    {
        $cashiers = $account->cashiers()
            ->with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('accounts/cashiers/index', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
            ],
            'cashiers' => $cashiers,
        ]);
    }

    public function store(
        StoreCashierRequest $request,
        Account $account,
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($account, $data): void {
            $user = User::query()->where('email', $data['email'])->firstOrFail();

            AccountMember::query()->firstOrCreate(
                [
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ],
                [
                    'role' => 'cashier',
                ],
            );

            Cashier::query()->firstOrCreate(
                [
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ],
                [
                    'name' => $data['name'] ?? $user->name,
                    'status' => 'active',
                ],
            );
        });

        return redirect()->route('accounts.cashiers.index', $account);
    }
}
