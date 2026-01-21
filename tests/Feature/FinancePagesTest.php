<?php

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
            ->where('totals.total_cents', 2000)
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
            ->where('totals.total_cents', 10000)
        );
});
