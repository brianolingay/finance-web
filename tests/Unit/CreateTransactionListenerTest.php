<?php

use App\Events\CashierSalaryCreated;
use App\Listeners\CreateTransactionListener;
use App\Models\Account;
use App\Models\CashierSalary;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('uses the cashier salary created_at when paid_at is missing', function () {
    $account = Account::factory()->create();

    $salary = new CashierSalary([
        'account_id' => $account->id,
        'cashier_id' => 1,
        'amount_cents' => 1500,
        'currency' => 'USD',
        'paid_at' => null,
        'status' => 'paid',
    ]);
    $salary->id = 123;
    $salary->created_at = now();

    app(CreateTransactionListener::class)->handle(new CashierSalaryCreated($salary));

    $transaction = Transaction::query()
        ->where('source_type', $salary->getMorphClass())
        ->where('source_id', $salary->getKey())
        ->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->occurred_at->toIso8601String())
        ->toBe($salary->created_at->toIso8601String());
});
