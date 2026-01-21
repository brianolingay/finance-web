<?php

use App\Actions\Ledger\RecordTransactionAction;
use App\DTOs\TransactionData;
use App\Models\Account;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('keeps the first transaction entry for the same source', function () {
    $account = Account::factory()->create();
    $income = Income::factory()->create([
        'account_id' => $account->id,
        'amount_cents' => 1200,
        'currency' => 'USD',
    ]);

    app(RecordTransactionAction::class)->run(new TransactionData(
        $account->id,
        'credit',
        $income->amount_cents,
        $income->currency,
        $income,
        $income->occurred_at,
    ));

    $updatedOccurredAt = now()->addDay();

    app(RecordTransactionAction::class)->run(new TransactionData(
        $account->id,
        'credit',
        2500,
        'EUR',
        $income,
        $updatedOccurredAt,
    ));

    $transaction = Transaction::query()->firstOrFail();

    expect(Transaction::query()->count())->toBe(1)
        ->and($transaction->amount_cents)->toBe(1200)
        ->and($transaction->currency)->toBe('USD')
        ->and($transaction->occurred_at->getTimestamp())->toBe($income->occurred_at->getTimestamp());
});

it('rejects invalid directions to protect CreateTransactionListener calls', function () {
    $account = Account::factory()->create();
    $income = Income::factory()->create(['account_id' => $account->id]);

    expect(fn () => app(RecordTransactionAction::class)->run(new TransactionData(
        $account->id,
        'invalid',
        $income->amount_cents,
        $income->currency,
        $income,
        $income->occurred_at,
    )))->toThrow(InvalidArgumentException::class);

    expect(Transaction::query()->count())->toBe(0);
});
