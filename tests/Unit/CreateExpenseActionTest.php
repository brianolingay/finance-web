<?php

use App\Actions\Finance\CreateExpenseAction;
use App\DTOs\ExpenseData;
use App\Events\ExpenseCreated;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates an expense and dispatches the event', function () {
    Event::fake();

    $account = Account::factory()->create();

    $expense = app(CreateExpenseAction::class)->run($account, new ExpenseData(
        null,
        'Office supplies',
        null,
        1200,
        'USD',
        null,
        null,
    ));

    expect($expense->account_id)->toBe($account->id)
        ->and($expense->amount_cents)->toBe(1200)
        ->and($expense->currency)->toBe('USD');

    Event::assertDispatched(ExpenseCreated::class, fn (ExpenseCreated $event): bool => $event->expense->is($expense));
});
