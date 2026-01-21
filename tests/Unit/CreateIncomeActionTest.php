<?php

use App\Actions\Finance\CreateIncomeAction;
use App\Actions\Ledger\RecordTransactionAction;
use App\Models\Account;
use App\Models\Income;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

use function Pest\Laravel\mock;

uses(TestCase::class, RefreshDatabase::class);

it('requires amount cents and currency', function (array $payload) {
    $account = Account::factory()->create();

    expect(fn () => app(CreateIncomeAction::class)->run($account, $payload))
        ->toThrow(ValidationException::class);
})->with([
    'missing amount' => [[
        'currency' => 'USD',
    ]],
    'missing currency' => [[
        'amount_cents' => 1200,
    ]],
]);

it('rolls back the income when transaction recording fails', function () {
    mock(RecordTransactionAction::class)
        ->shouldReceive('run')
        ->andThrow(new RuntimeException('Ledger failure.'));

    $account = Account::factory()->create();

    expect(fn () => app(CreateIncomeAction::class)->run($account, [
        'amount_cents' => 1500,
        'currency' => 'USD',
    ]))->toThrow(RuntimeException::class);

    expect(Income::query()->count())->toBe(0);
});
