<?php

use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

it('prevents cashiers from accessing finance routes', function () {
    $owner = User::factory()->create();
    $account = Account::factory()->create(['owner_user_id' => $owner->id]);
    $cashier = User::factory()->create();

    AccountMember::factory()->create([
        'account_id' => $account->id,
        'user_id' => $cashier->id,
        'role' => 'cashier',
    ]);

    $this->actingAs($cashier)
        ->get(route('accounts.expenses.index', $account))
        ->assertForbidden();
});

it('allows owners to access finance routes', function () {
    $owner = User::factory()->create();
    $account = Account::factory()->create(['owner_user_id' => $owner->id]);

    $this->actingAs($owner)
        ->get(route('accounts.expenses.index', $account))
        ->assertSuccessful();
});
