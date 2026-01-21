<?php

use App\Actions\Finance\CreateIncomeAction;
use App\Actions\Ledger\RecordTransactionAction;
use App\DTOs\IncomeData;
use App\Models\Account;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Laravel\mock;

uses(TestCase::class, RefreshDatabase::class);

it('creates income entries for an account', function () {
    $account = Account::factory()->create();

    $income = app(CreateIncomeAction::class)->run($account, new IncomeData(
        null,
        'Client payment',
        null,
        1200,
        'USD',
        null,
        null,
    ));

    expect($income->account_id)->toBe($account->id)
        ->and($income->amount_cents)->toBe(1200)
        ->and($income->currency)->toBe('USD');
});

it('rolls back the income when transaction recording fails', function () {
    mock(RecordTransactionAction::class)
        ->shouldReceive('run')
        ->andThrow(new RuntimeException('Ledger failure.'));

    $account = Account::factory()->create();

    expect(fn () => app(CreateIncomeAction::class)->run($account, new IncomeData(
        null,
        null,
        null,
        1500,
        'USD',
        null,
        null,
    )))->toThrow(RuntimeException::class);

    expect(Income::query()->count())->toBe(0);
});
