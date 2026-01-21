<?php

use App\Actions\Ledger\RecordTransactionAction;
use App\DTOs\TransactionData;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

it('returns expense totals on the expenses index', function () {
    $account = Account::factory()->create();
    $owner = User::query()->findOrFail($account->owner_user_id);

    Expense::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 1200,
    ]);

    Expense::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 800,
    ]);

    $this->actingAs($owner)
        ->get(route('accounts.expenses.index', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('accounts/expenses/index')
            ->where('totals.total_cents.PHP', 2000)
        );
});

it('returns income totals on the incomes index', function () {
    $account = Account::factory()->create();
    $owner = User::query()->findOrFail($account->owner_user_id);

    Income::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 5400,
    ]);

    Income::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 4600,
    ]);

    $this->actingAs($owner)
        ->get(route('accounts.incomes.index', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('accounts/incomes/index')
            ->where('totals.total_cents.PHP', 10000)
        );
});

it('returns dashboard totals grouped by currency', function () {
    $account = Account::factory()->create();
    $owner = User::query()->findOrFail($account->owner_user_id);

    $income = Income::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 5000,
        'currency' => 'PHP',
    ]);

    $expense = Expense::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 2000,
        'currency' => 'PHP',
    ]);

    app(RecordTransactionAction::class)->run(new TransactionData(
        $account->id,
        'credit',
        $income->amount_cents,
        $income->currency,
        $income,
        $income->occurred_at,
    ));

    app(RecordTransactionAction::class)->run(new TransactionData(
        $account->id,
        'debit',
        $expense->amount_cents,
        $expense->currency,
        $expense,
        $expense->occurred_at,
    ));

    $this->actingAs($owner)
        ->get(route('accounts.dashboard', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('accounts/dashboard')
            ->where('totals.credit_cents.PHP', 5000)
            ->where('totals.debit_cents.PHP', 2000)
            ->where('totals.net_cents.PHP', 3000)
        );
});
